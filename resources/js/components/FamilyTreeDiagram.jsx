import React from 'react';
import { Page, View, Text, StyleSheet } from '@react-pdf/renderer';

const styles = StyleSheet.create({
  page: {
    padding: 10,
    backgroundColor: '#ffffff',
    fontFamily: 'Corben',
  },
  title: {
    fontSize: 16,
    fontFamily: 'Great Vibes',
    marginBottom: 10,
    textAlign: 'center',
  },
  treeContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  },
  personContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  },
  personText: {
    fontSize: 8,
    textAlign: 'center',
    marginBottom: 2,
  },
  coupleContainer: {
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  childrenContainer: {
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'center',
  },
  verticalLine: {
    width: 1,
    backgroundColor: 'black',
    height: 10,
  },
  horizontalLine: {
    height: 1,
    backgroundColor: 'black',
    alignSelf: 'center',
  },
});

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase();
};

const PersonNode = ({ person }) => (
  <View style={styles.personContainer}>
    <Text style={styles.personText}>{getInitials(person.data.name)}</Text>
  </View>
);

const FamilyNode = ({ person, graph, selectedPeople, processedPeople }) => {
  if (processedPeople.has(person.id)) return null;
  processedPeople.add(person.id);

  const spouse = graph.edges.find(edge => 
    (edge.source === person.id || edge.target === person.id) && edge.label === 'Spouse'
  );
  const spousePerson = spouse 
    ? graph.nodes.find(node => node.id === (spouse.source === person.id ? spouse.target : spouse.source))
    : null;

  const children = graph.edges
    .filter(edge => edge.source === person.id && edge.label === 'Child')
    .map(edge => graph.nodes.find(node => node.id === edge.target))
    .filter(child => selectedPeople.includes(child.id));

  const childSpacing = 40;

  return (
    <View style={styles.personContainer}>
      <View style={styles.coupleContainer}>
        <PersonNode person={person} />
        {spousePerson && selectedPeople.includes(spousePerson.id) && (
          <>
            <View style={[styles.horizontalLine, { width: 10 }]} />
            <PersonNode person={spousePerson} />
          </>
        )}
      </View>
      {children.length > 0 && (
        <>
          <View style={styles.verticalLine} />
          <View style={[styles.horizontalLine, { width: (children.length - 1) * childSpacing }]} />
          <View style={styles.childrenContainer}>
            {children.map((child, index) => (
              <View key={child.id} style={{ alignItems: 'center', width: childSpacing }}>
                <View style={[styles.verticalLine, { height: 10 }]} />
                <FamilyNode 
                  person={child} 
                  graph={graph} 
                  selectedPeople={selectedPeople}
                  processedPeople={processedPeople}
                />
              </View>
            ))}
          </View>
        </>
      )}
    </View>
  );
};

const FamilyTreePage = ({ rootPerson, graph, selectedPeople }) => {
  const processedPeople = new Set();
  return (
    <Page size="A5" orientation="landscape" style={styles.page}>
      <Text style={styles.title}>Family Tree</Text>
      <View style={styles.treeContainer}>
        <FamilyNode 
          person={rootPerson} 
          graph={graph} 
          selectedPeople={selectedPeople}
          processedPeople={processedPeople}
        />
      </View>
    </Page>
  );
};

const FamilyTreeDiagram = ({ selectedPeople, graph }) => {
  const findRootMembers = () => {
    const roots = new Set(selectedPeople);
    selectedPeople.forEach(id => {
      const parentEdges = graph.edges.filter(edge => edge.target === id && edge.label === 'Child');
      parentEdges.forEach(edge => {
        if (selectedPeople.includes(edge.source)) {
          roots.delete(id);
        }
      });
    });
    return Array.from(roots);
  };

  const rootMembers = findRootMembers();

  return rootMembers.map((rootId, index) => {
    const rootPerson = graph.nodes.find(node => node.id === rootId);
    return (
      <FamilyTreePage 
        key={rootPerson.id} 
        rootPerson={rootPerson} 
        graph={graph} 
        selectedPeople={selectedPeople}
      />
    );
  });
};

export default FamilyTreeDiagram;