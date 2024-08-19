import React, { useState, useEffect } from 'react';
import { Document, Page, Text, View, StyleSheet, Font, PDFViewer, Link } from '@react-pdf/renderer';
import axios from 'axios';

Font.register({
  family: 'Great Vibes',
  src: "http://fonts.gstatic.com/s/greatvibes/v4/6q1c0ofG6NKsEhAc2eh-3Z0EAVxt0G0biEntp43Qt6E.ttf"
});

Font.register({
  family: 'Corben',
  src: "http://fonts.gstatic.com/s/corben/v9/aAJbkLknKhfXxsbVwcGZiA.ttf",
});

const styles = StyleSheet.create({ //CSS
  page: {
    padding: 30,
    backgroundColor: '#ffffff',
    fontFamily: 'Corben',
  },
  titlePage: {
    display: 'flex',
    flexDirection: 'column',
    justifyContent: 'center',
    alignItems: 'center',
    height: '100%',
    width: '100%',
  },
  title: {
    fontSize: 24,
    fontFamily: 'Great Vibes',
    fontWeight: 'bold',
    textAlign: 'center',
    marginTop: 200,
  },
  content: {
    marginLeft: 30,
  },
  contentsTitle: {
    fontSize: 28,
    fontFamily: 'Great Vibes',
    marginBottom: 20,
    textAlign: 'center',
  },
  contentItem: {
    marginBottom: 5,
    fontSize: 8,
  },
  link: {
    color: 'black',
    textDecoration: 'none',
  },
  border: {
    border: '1pt solid black',
    width: '100%',
    height: '100%',
    padding: 30,
    boxSizing: 'border-box',
  },
});

const TitlePage = ({ title }) => ( //title page
  <Page size="A5" style={styles.page}>
    <View style={styles.titlePage}>
      <View style={styles.border}>
       <Text style={styles.title}>{title}</Text>
      </View>
    </View>
  </Page>
);

const ContentsPage = ({ graph }) => ( //contents page
  <Page size="A5" style={styles.page}>
   <View style={styles.border}>
      <Text style={styles.contentsTitle}>Contents</Text>
      <View style={styles.content}>
      {renderContents(graph)}
      </View>
    </View>
  </Page>
);

const renderContents = (graph) => { //render each person in contents
  const allPeople = new Set();

  const renderContentsItem = (id) => {
    if (allPeople.has(id)) {
      return null;
    }
    allPeople.add(id);

    const person = graph.nodes.find(node => node.id === id);
    if (!person) return null;

    const mainPerson = (
      <Link key={person.id} src={`#${person.id}`} style={styles.link}>
        {person.data.name}
      </Link>
    );

    const spouses = graph.edges
      .filter(edge => edge.source === id && edge.label === 'Spouse')
      .map(edge => {
        const spouseId = edge.target;
        if (allPeople.has(spouseId)) return null;
        allPeople.add(spouseId);
        const spouse = graph.nodes.find(node => node.id === spouseId);
        return (
          <Link key={spouse.id} src={`#${spouse.id}`} style={styles.link}>
            {spouse.data.name}
          </Link>
        );
      });

    const couple = [mainPerson, ...spouses].reduce((prev, curr) => [prev, ' & ', curr]);

    return (
      <React.Fragment key={person.id}>
        <Text style={styles.contentItem}>
          {couple}
        </Text>
      </React.Fragment>
    );
  };

  return graph.nodes.map(node => renderContentsItem(node.id));
};

const PersonPage = ({ person }) => {
  if (!person || !person.data) {
    return (
      <Page size="A5" style={styles.page}>
        <View style={styles.border}>
          <Text style={styles.contentsTitle}>Unknown Person</Text>
        </View>
      </Page>
    );
  }

  return (
    <Page size="A5" style={styles.page} id={person.id}>
      <View style={styles.border}>
        <Text style={styles.contentsTitle}>{person.data.name}</Text>
      </View>
    </Page>
  );
};

const FamilyTreePDF = ({ onClose }) => { //fetch user's name and family data from backend
  const [userName, setUserName] = useState('');
  const [familyData, setFamilyData] = useState({ nodes: [], edges: [] });

  useEffect(() => {
    const fetchData = async () => {
      try {
        const userResponse = await axios.get('/api/user', {
          withCredentials: true,
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        setUserName(userResponse.data.name);

        const familyResponse = await axios.get('/api/family-graph-json');
        setFamilyData(familyResponse.data);
      } catch (error) {
        console.error('Error fetching data:', error);
        setUserName('User');
      }
    };

    fetchData();
  }, []);

  const renderPages = (graph) => {
    const allPeople = new Set();
    
    const renderPerson = (person) => {
      if (allPeople.has(person.id)) {
        return [];
      }
      allPeople.add(person.id);

      const pages = [<PersonPage key={person.id} person={person} graph={graph} />];

      graph.edges.forEach(edge => {
        if (edge.source === person.id && edge.label === 'Spouse') {
          const spouse = graph.nodes.find(node => node.id === edge.target);
          if (spouse && !allPeople.has(spouse.id)) {
            pages.push(<PersonPage key={spouse.id} person={spouse} graph={graph} />);
            allPeople.add(spouse.id);
          }
        }
      });

      graph.edges.forEach(edge => {
        if (edge.source === person.id && edge.label === 'Child') {
          const child = graph.nodes.find(node => node.id === edge.target);
          if (child) {
            pages.push(...renderPerson(child));
          }
        }
      });
  
      return pages;
    };
  
    return graph.nodes.flatMap(node => renderPerson(node));
  };

  const overlay = { // position when viewing PDF
    position: 'fixed',
    top: 0,
    left: 0,
    width: '100%',
    height: '100%',
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 1000,
  };

  const closeButton = { //button to close PDF view
    position: 'absolute',
    top: '10px',
    right: '10px',
    padding: '5px 10px',
    backgroundColor: '#CCE7BD',
    color: '#A7B492',
    border: 'none',
    borderRadius: '5px',
    cursor: 'pointer',
    fontSize: '1em',
    fontFamily: '"Inika", serif',
    fontWeight: 'bold',
  };

  return (
    <div style={overlay}>
      <button style={closeButton} onClick={onClose}>Close</button>
      <PDFViewer width="80%" height="80%">
        <Document>
          <TitlePage title={`${userName}'s Family Book`} />
          <ContentsPage graph={familyData} />
          {renderPages(familyData)}
        </Document>
      </PDFViewer>
    </div>
  );
};

export default FamilyTreePDF;