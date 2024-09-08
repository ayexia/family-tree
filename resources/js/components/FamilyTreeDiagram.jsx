import React from 'react';
import { Page, View, Text, StyleSheet } from '@react-pdf/renderer';

const styles = StyleSheet.create({
  page: {
    padding: 10,
    backgroundColor: '#ffffff',
    fontFamily: 'Corben',
  },
  title: {
    fontSize: 20,
    fontFamily: 'Great Vibes',
    marginBottom: 10,
    textAlign: 'center',
  },
  treeContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  },
  personInitials: {
    fontSize: 10,
    margin: '5 0',
    textAlign: 'center',
  },
  childrenContainer: {
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'center',
  },
  verticalLine: {
    width: 1,
    backgroundColor: '#000',
    alignSelf: 'center',
  },
  spouseLine: {
    width: 10,
    height: 1,
    backgroundColor: '#000',
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

const PersonInitials = ({ person }) => (
  <Text style={styles.personInitials}>{getInitials(person.data.name)}</Text>
);

const FamilyNode = ({ person, graph, depth = 0 }) => {
  const spouse = graph.edges
    .find(edge => edge.source === person.id && edge.label === 'Spouse');
  const spousePerson = spouse ? graph.nodes.find(node => node.id === spouse.target) : null;

  const children = graph.edges
    .filter(edge => edge.source === person.id && edge.label === 'Child')
    .map(edge => graph.nodes.find(node => node.id === edge.target));

  return (
    <View style={{ alignItems: 'center' }}>
      <View style={{ flexDirection: 'row', alignItems: 'center' }}>
        <PersonInitials person={person} />
        {spousePerson && (
          <>
            <View style={styles.spouseLine} />
            <PersonInitials person={spousePerson} />
          </>
        )}
      </View>
      {children.length > 0 && (
        <>
          <View style={[styles.verticalLine, { height: 10 }]} />
          <View style={styles.childrenContainer}>
            {children.map((child, index) => (
              <React.Fragment key={child.id}>
                {index > 0 && <View style={{ width: 20 }} />}
                <FamilyNode person={child} graph={graph} depth={depth + 1} />
              </React.Fragment>
            ))}
          </View>
        </>
      )}
    </View>
  );
};

const FamilyTreePage = ({ rootPerson, graph }) => (
  <Page size="A5" orientation="landscape" style={styles.page}>
    <Text style={styles.title}>Family Tree</Text>
    <View style={styles.treeContainer}>
      <FamilyNode person={rootPerson} graph={graph} />
    </View>
  </Page>
);

const FamilyTreeDiagram = ({ graph }) => {
  const rootMembers = graph.nodes.filter(node => 
    !graph.edges.some(edge => edge.target === node.id && edge.label === 'Child')
  );

  return rootMembers.map((rootPerson, index) => (
    <FamilyTreePage key={rootPerson.id} rootPerson={rootPerson} graph={graph}/>
  ));
};

export default FamilyTreeDiagram;