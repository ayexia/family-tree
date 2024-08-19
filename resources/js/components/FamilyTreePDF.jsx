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

const ContentsPage = ({ data }) => (
  <Page size="A5" style={styles.page}>
   <View style={styles.border}>
      <Text style={styles.contentsTitle}>Contents</Text>
      <View style={styles.content}>
      {renderContents(data)}
      </View>
    </View>
  </Page>
);

const renderContents = (data) => {
  const allPeople = new Set();

  const renderContentsItem = (person, level = 0) => {
    if (allPeople.has(person.id)) {
      return null;
    }
    allPeople.add(person.id);

    const indent = '....'.repeat(level);

    const mainPerson = (
      <Link key={person.id} src={`#${person.id}`} style={styles.link}>
        {person.name}
      </Link>
    );

    const spouses = (person.spouses || [])
    .filter(spouse => !allPeople.has(spouse.id))
    .map(spouse => {
      allPeople.add(spouse.id);
      return (
          <Link key={spouse.id} src={`#${spouse.id}`} style={styles.link}>
            {spouse.name}
          </Link>
        );
      });

    const couple = [mainPerson, ...spouses].reduce((prev, curr) => [prev, ' & ', curr]);

    return (
      <React.Fragment key={person.id}>
        <Text style={styles.contentItem}>
          {indent}
          {couple}
        </Text>
        {(person.children || []).map(child => renderContentsItem(child, level + 1))}
      </React.Fragment>
    );
  };

  return data.map(person => renderContentsItem(person));
};

const PersonPage = ({ person }) => (
  <Page size="A5" style={styles.page} id={person.id}>
    <View style={styles.border}>
    <Text style={styles.contentsTitle}>{person.name}</Text>
    </View>
  </Page>
);

const FamilyTreePDF = ({ onClose }) => { //fetch user's name from backend
  const [userName, setUserName] = useState('');
  const [familyData, setFamilyData] = useState([]);

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

        const familyResponse = await axios.get('/api/family-tree-json');
        setFamilyData(buildFamilyTree(familyResponse.data));
      } catch (error) {
        console.error('Error fetching data:', error);
        setUserName('User');
      }
    };

    fetchData();
  }, []);

  const buildFamilyTree = (data) => {
    const visited = new Set();
    const processPerson = (person) => {
      if (visited.has(person.id)) {
        return null;
      }
      visited.add(person.id);
      return {
        ...person,
        children: (person.children || []).map(processPerson).filter(Boolean),
        spouses: (person.spouses || []).map(processPerson).filter(Boolean),
      };
    };
    return (data || []).map(processPerson).filter(Boolean);
  };

  const renderPages = (data) => {
    const allPeople = new Set();
    
    const renderPerson = (person) => {
      if (allPeople.has(person.id)) {
        return [];
      }
      allPeople.add(person.id);

      const pages = [<PersonPage key={person.id} person={person} />];

      if (person.spouses) {
        person.spouses.forEach(spouse => {
          if (!allPeople.has(spouse.id)) {
            pages.push(<PersonPage key={spouse.id} person={spouse} />);
            allPeople.add(spouse.id);
          }
        });
      }

      if (person.children) {
        person.children.forEach(child => {
          pages.push(...renderPerson(child));
        });
      }

      return pages;
    };

    return data.flatMap(renderPerson);
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
          <ContentsPage data={familyData} />
          {renderPages(familyData)}
        </Document>
      </PDFViewer>
    </div>
  );
};

export default FamilyTreePDF;