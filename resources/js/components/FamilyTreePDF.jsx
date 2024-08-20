import React, { useState, useEffect } from 'react';
import { Document, Page, Text, View, StyleSheet, Font, PDFViewer, Link, Image } from '@react-pdf/renderer';
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
    display: 'flex',
    flexDirection: 'column',
  },
  name: {
    fontSize: 24,
    fontFamily: 'Great Vibes',
    marginBottom: 20,
    textAlign: 'center',
  },
  photoPlaceholder: {
    width: 150,
    height: 150,
    borderRadius: 75,
    backgroundColor: '#E0E0E0',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    alignSelf: 'center',
  },
  photo: {
    width: 150,
    height: 150,
    borderRadius: 75,
    marginBottom: 20,
    alignSelf: 'center',
  },
  photoText: {
    fontSize: 12,
    color: '#666666',
  },
  biographyTitle: {
    fontSize: 20,
    fontFamily: 'Great Vibes',
    marginBottom: 5,
    textAlign: 'left',
  },
  biographyBox: {
    border: '1pt solid black',
    padding: 10,
    marginTop: 'auto',
    flexGrow: 1,
  },
  biographyText: {
    fontSize: 10,
    lineHeight: 1.5,
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

const formatDate = (dateString) => {
  if (!dateString) return null;
  const date = new Date(dateString);
  if (isNaN(date.getTime())) return null;
  
  const day = date.getUTCDate();
  const month = date.toLocaleString('default', { month: 'long', timeZone: 'UTC' });
  const year = date.getUTCFullYear();
  
  if (day === 1 && date.getUTCMonth() === 0) {
    return `${year}`;
  } else if (day === 1) {
    return `${month} ${year}`;
  } else {
    return `${day} ${month} ${year}`;
  }
};

const PersonPage = ({ person, graph }) => {
  if (!person || !person.data) {
    return (
      <Page size="A5" style={styles.page}>
        <View style={styles.border}>
          <Text style={styles.name}>Unknown Person</Text>
        </View>
      </Page>
    );
  }

  const { data } = person;

  const spouses = graph.edges
    .filter(edge => edge.source === person.id && edge.label === 'Spouse')
    .map((edge, index) => {
      const spouseNode = graph.nodes.find(node => node.id === edge.target);
      return {
        ...spouseNode,
        marriageDate: data.marriage_dates && data.marriage_dates[index] ? formatDate(data.marriage_dates[index]) : null,
        divorceDate: data.divorce_dates && data.divorce_dates[index] ? formatDate(data.divorce_dates[index]) : null,
        isCurrent: edge.is_current
      };
    });

  const children = graph.edges
    .filter(edge => edge.source === person.id && edge.label === 'Child')
    .map(edge => graph.nodes.find(node => node.id === edge.target));

  const parents = data.parents && typeof data.parents === 'object' ? Object.values(data.parents) : [];

  const generateBiography = () => {
    const bio = [];
    
    const birthDate = formatDate(data.birth_date);
    const hasKnownParents = parents.length > 0;
  
    if (!birthDate && !hasKnownParents) {
      bio.push(`${data.name}'s date of birth is unknown. `);
    } else if (!birthDate && hasKnownParents) {
      bio.push(`${data.name}'s date of birth is unknown, born to parents `);
      parents.forEach((p, index) => {
        if (index > 0) bio.push(' and ');
        bio.push(
          <Link key={p.id} src={`#${p.id}`} style={styles.link}>
            {p.name}
          </Link>
        );
      });
      bio.push('. ');
    } else if (birthDate && !hasKnownParents) {
      bio.push(`${data.name} was born ${birthDate.includes(' ') ? 'on' : 'in'} ${birthDate}. `);
    } else if (birthDate && hasKnownParents) {
      bio.push(`${data.name} was born ${birthDate.includes(' ') ? 'on' : 'in'} ${birthDate} to parents `);
      parents.forEach((p, index) => {
        if (index > 0) bio.push(' and ');
        bio.push(
          <Link key={p.id} src={`#${p.id}`} style={styles.link}>
            {p.name}
          </Link>
        );
      });
      bio.push('. ');
    }
  
    if (spouses.length > 0) {
      bio.push(`${data.gender === 'M' ? 'He' : 'She'} `);
      if (spouses.length === 1) {
        const spouse = spouses[0];
        bio.push(`married `);
        bio.push(
          <Link key={spouse.id} src={`#${spouse.id}`} style={styles.link}>
            {spouse.data.name}
          </Link>
        );
        if (spouse.marriageDate) {
          bio.push(` ${spouse.marriageDate.includes(' ') ? 'on' : 'in'} ${spouse.marriageDate}`);
        }
        if (spouse.divorceDate && !spouse.isCurrent) {
          bio.push(` and divorced ${spouse.divorceDate.includes(' ') ? 'on' : 'in'} ${spouse.divorceDate}`);
        }
        bio.push('. ');
      } else {
        bio.push('married ');
        spouses.forEach((spouse, index) => {
          if (index > 0) bio.push(index === spouses.length - 1 ? ' and ' : ', ');
          bio.push(
            <Link key={spouse.id} src={`#${spouse.id}`} style={styles.link}>
              {spouse.data.name}
            </Link>
          );
          if (spouse.marriageDate) {
            bio.push(` ${spouse.marriageDate.includes(' ') ? 'on' : 'in'} ${spouse.marriageDate}`);
          }
          if (spouse.divorceDate && !spouse.isCurrent) {
            bio.push(` (divorced ${spouse.divorceDate.includes(' ') ? 'on' : 'in'} ${spouse.divorceDate})`);
          }
          if (spouse.isCurrent) {
            bio.push(' (current spouse)');
          }
        });
        bio.push('. ');
      }
    }
  
    if (children.length > 0) {
      bio.push(`${data.gender === 'M' ? 'He' : 'She'} had ${children.length} ${children.length === 1 ? 'child' : 'children'}: `);
      children.forEach((child, index) => {
        if (index > 0) bio.push(', ');
        bio.push(
          <Link key={child.id} src={`#${child.id}`} style={styles.link}>
            {child.data.name}
          </Link>
        );
      });
      bio.push('. ');
    }
  
    const deathDate = formatDate(data.death_date);
    if (deathDate) {
      bio.push(`${data.gender === 'M' ? 'He' : 'She'} passed away ${deathDate.includes(' ') ? 'on' : 'in'} ${deathDate}.`);
    } else if (data.death_date === null) {
      bio.push(`${data.gender === 'M' ? 'His' : 'Her'} date of death is unknown.`);
    }
  
    return bio;
  };  

  return (
    <Page size="A5" style={styles.page} id={person.id}>
      <View style={styles.border}>
        <Text style={styles.name}>{data.name}</Text>
        
        {data.image ? (
          <Image
            src={data.image}
            style={styles.photo}
          />
        ) : (
          <View style={styles.photoPlaceholder}>
            <Text style={styles.photoText}>Photo</Text>
          </View>
        )}

        <Text style={styles.biographyTitle}>Biography</Text>
        <View style={styles.biographyBox}>
          <Text style={styles.biographyText}>
            {generateBiography()}
          </Text>
        </View>
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