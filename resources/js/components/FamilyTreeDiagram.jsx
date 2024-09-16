import React from 'react';
import { Page, View, Text, StyleSheet, Link } from '@react-pdf/renderer';

const styles = StyleSheet.create({
  page: {
    padding: 10,
    backgroundColor: '#ffffff',
    fontFamily: 'Corben',
  },
  title: {
    fontSize: 14,
    fontFamily: 'Great Vibes',
    marginBottom: 10,
    textAlign: 'center',
  },
  treeContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  },
  familyContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    marginBottom: 15,
  },
  personContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    margin: 2,
  },
  personText: {
    fontSize: 8,
    textAlign: 'center',
  },
  rootText: {
    fontSize: 8,
    textAlign: 'center',
    fontWeight: 'bold',
    backgroundColor: '#E6E6FA',
    padding: 2,
    borderRadius: 3,
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

const PersonNode = ({ person, isRoot }) => (
  <Link src={`#${person.id}`}>
  <View style={styles.personContainer}>
    <Text style={isRoot ? styles.rootText : styles.personText}>
      {getInitials(person.data.name)}
    </Text>
  </View>
</Link>
);

const FamilyNode = ({ person, graph, selectedPeople, isRoot }) => {
  const spouse = graph.edges.find(edge =>
    (edge.source === person.id || edge.target === person.id) && edge.label === 'Spouse'
  );
    const spousePerson = spouse
    ? graph.nodes.find(node => node.id === (spouse.source === person.id ? spouse.target : spouse.source))
    : null;

  const children = graph.edges
    .filter(edge => 
    (edge.source === person.id || (spousePerson && edge.source === spousePerson.id)) && 
    edge.label === 'Child'
    )
    .map(edge => graph.nodes.find(node => node.id === edge.target))
    .filter(child => selectedPeople.includes(child.id))
    .filter((child, index, self) =>
    index === self.findIndex((t) => t.id === child.id)
  );

  const childWidth = 30;

  return (
    <View style={styles.familyContainer}>
      <View style={styles.coupleContainer}>
        <PersonNode person={person} isRoot={isRoot} />
        {spousePerson && selectedPeople.includes(spousePerson.id) && (
          <>
            <View style={[styles.horizontalLine, { width: 5 }]} />
            <PersonNode person={spousePerson} />
          </>
        )}
      </View>
      {children.length > 0 && (
        <>
          <View style={styles.verticalLine} />
          <View style={[styles.horizontalLine, { width: Math.max((children.length - 1) * childWidth, 0) }]} />
          <View style={styles.childrenContainer}>
            {children.map((child) => (
            <View key={child.id} style={{ alignItems: 'center', width: childWidth }}>
            <View style={styles.verticalLine} />
              <PersonNode person={child} />
              </View>
            ))}
          </View>
        </>
      )}
    </View>
  );
};

const FamilyTreePage = ({ families, graph, selectedPeople, generation }) => (
    <Page size="A5" orientation="landscape" style={styles.page}>
    <Text style={styles.title}>Family Tree - Generation {generation}</Text>
      <View style={styles.treeContainer}>
      {families.map((rootPerson) => (
        <FamilyNode
          key={rootPerson.id}
          person={rootPerson}
          graph={graph}
          selectedPeople={selectedPeople}
          isRoot={true}
        />
      ))}
      </View>
    </Page>
  );

  const FamilyTreeDiagram = ({ selectedPeople, graph }) => {
  const getGeneration = (person) => {
  let generation = 0;
  let currentPerson = person;
  while (currentPerson) {
    const parent = graph.edges.find(edge => 
      edge.target === currentPerson.id && edge.label === 'Child'
    );
    if (parent) {
      generation++;
      currentPerson = graph.nodes.find(node => node.id === parent.source);
    } else {
      break;
    }
  }
  return generation;
};

  const familiesByGeneration = {};

    selectedPeople.forEach(id => {
    const person = graph.nodes.find(node => node.id === id);
    if (person) {
      const generation = getGeneration(person);
      if (!familiesByGeneration[generation]) {
        familiesByGeneration[generation] = [];
        }
      familiesByGeneration[generation].push(person);
      }
    });

  const pages = [];
  Object.entries(familiesByGeneration).forEach(([generation, families]) => {
  for (let i = 0; i < families.length; i += 4) {
    const familiesToShow = families.slice(i, i + 4);
    pages.push(
      <FamilyTreePage
        key={`page-${pages.length}`}
        families={familiesToShow}
        graph={graph}
        selectedPeople={selectedPeople}
        generation={parseInt(generation) + 1}
      />
    );
  }
  });

  return pages;
};

export default FamilyTreeDiagram;