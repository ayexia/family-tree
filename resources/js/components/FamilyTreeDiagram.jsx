  //imports required components from react and react-pdf
  import React from 'react';
  import { Page, View, Text, StyleSheet, Link } from '@react-pdf/renderer';

  //defines styles for pdf components
  const styles = StyleSheet.create({
  //styling for page layout
  page: {
    padding: 10,
    backgroundColor: '#ffffff',
    fontFamily: 'Corben',
  },
  //styling for page title
  title: {
    fontSize: 14,
    fontFamily: 'Great Vibes',
    marginBottom: 10,
    textAlign: 'center',
  },
  //styling for main tree container
  treeContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  },
  //styling for each family unit container
  familyContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    marginBottom: 15,
  },
  //styling for individual person boxes
  personContainer: {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
    margin: 2,
  },
  //styling for person's name text
  personText: {
    fontSize: 8,
    textAlign: 'center',
  },
  //special styling for root person text
  rootText: {
    fontSize: 8,
    textAlign: 'center',
    fontWeight: 'bold',
    backgroundColor: '#E6E6FA',
    padding: 2,
    borderRadius: 3,
  },
  //styling for couple pairing container
  coupleContainer: {
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  //styling for children container
  childrenContainer: {
    display: 'flex',
    flexDirection: 'row',
    justifyContent: 'center',
  },
  //styling for vertical connection lines
  verticalLine: {
    width: 1,
    backgroundColor: 'black',
    height: 10,
  },
  //styling for horizontal connection lines
  horizontalLine: {
    height: 1,
    backgroundColor: 'black',
    alignSelf: 'center',
  },
  });

  //converts full name to initials (e.g., "John Smith" -> "JS")
  const getInitials = (name) => {
    return name
      .split(' ')               //splits name into array of words
      .map(n => n[0])          //gets first letter of each word
      .join('')                //combines letters
      .toUpperCase();          //converts to uppercase
  };
  
  //creates clickable node showing person's initials
  const PersonNode = ({ person, isRoot }) => (
    <Link src={`#${person.id}`}>
      <View style={styles.personContainer}>
        <Text style={isRoot ? styles.rootText : styles.personText}>
          {getInitials(person.data.name)}
        </Text>
      </View>
    </Link>
  );
  
  //creates family unit showing relationships between people
  const FamilyNode = ({ person, graph, selectedPeople, isRoot }) => {
    //finds spouse relationship in graph data
    const spouse = graph.edges.find(edge =>
      (edge.source === person.id || edge.target === person.id) && edge.label === 'Spouse'
    );
    
    //gets spouse's details if they exist
    const spousePerson = spouse
      ? graph.nodes.find(node => node.id === (spouse.source === person.id ? spouse.target : spouse.source))
      : null;
  
    //finds all children of the couple and filters for selected ones
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
  
    //width for spacing between children
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

    //creates a single page of the family tree PDF
  const FamilyTreePage = ({ families, graph, selectedPeople, generation }) => (
    //sets up A5 landscape page with defined styles
    <Page size="A5" orientation="landscape" style={styles.page}>
      <Text style={styles.title}>Family Tree - Generation {generation}</Text>
      <View style={styles.treeContainer}>
        {families.map((rootPerson) => (
          //creates family unit for each root person
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
  
  //main component that generates complete family tree PDF
  const FamilyTreeDiagram = ({ selectedPeople, graph }) => {
    //calculates how many generations back a person is
    const getGeneration = (person) => {
      let generation = 0;
      let currentPerson = person;
      //keeps checking for parents until reaching the oldest generation
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
  
    //organises families by their generation number
    const familiesByGeneration = {};
  
    //processes each selected person
    selectedPeople.forEach(id => {
      const person = graph.nodes.find(node => node.id === id);
      if (person) {
        const generation = getGeneration(person);
        //creates array for generation if it doesn't exist
        if (!familiesByGeneration[generation]) {
          familiesByGeneration[generation] = [];
        }
        //adds person to their generation group
        familiesByGeneration[generation].push(person);
      }
    });
  
    //creates pages for PDF, limiting to 4 families per page
    const pages = [];
    Object.entries(familiesByGeneration).forEach(([generation, families]) => {
      //splits families into groups of 4
      for (let i = 0; i < families.length; i += 4) {
        const familiesToShow = families.slice(i, i + 4);
        //creates new page for each group
        pages.push(
          <FamilyTreePage
            key={`page-${pages.length}`}
            families={familiesToShow}
            graph={graph}
            selectedPeople={selectedPeople}
            //adds 1 to generation number for display (so it starts at 1 not 0)
            generation={parseInt(generation) + 1}
          />
        );
      }
    });
  
    //returns all pages for PDF
    return pages;
  };
  
  //makes component available for use in other parts of application
  export default FamilyTreeDiagram;