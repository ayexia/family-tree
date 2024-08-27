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
    fontSize: 30,
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
    textDecoration: 'underline',
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
    fontSize: 28,
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
    fontSize: 9,
    lineHeight: 1.5,
    wordBreak: 'break-word',
  },
  timelinePage: {
    padding: 40,
    backgroundColor: '#ffffff',
    fontFamily: 'Corben',
  },
  timelineTitle: {
    fontSize: 20,
    fontFamily: 'Great Vibes',
    marginBottom: 30,
    textAlign: 'center',
  },
  timelineContainer: {
    flexDirection: 'column',
    position: 'relative',
    paddingLeft: 20,
  },
  timelineLine: {
    position: 'absolute',
    left: 10,
    top: 0,
    bottom: 0,
    width: 1,
    backgroundColor: 'black',
  },
  timelineEvent: {
    flexDirection: 'row',
    marginBottom: 5,
    alignItems: 'center',
  },
  timelineDate: {
    width: '30%',
    fontSize: 8,
    textAlign: 'right',
    paddingRight: 5,
  },
  timelineDescription: {
    width: '70%',
    fontSize: 8,
    paddingLeft: 5,
  },
  pageNumber: {
    fontSize: 8,
    textAlign: 'right'
  }
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

const ITEMS_PER_PAGE = 20;

const ContentsPage = ({ graph }) => { //contents page - allows 20 entries per page before breaking
  const contentItems = renderContents(graph).filter(item => item !== null);
  const pageCount = Math.ceil(contentItems.length / ITEMS_PER_PAGE);

  return Array.from({ length: pageCount }, (_, pageIndex) => (
    <Page key={`contents-${pageIndex}`} size="A5" style={styles.page}>
      <View style={styles.border}>
        {pageIndex === 0 && <Text style={styles.contentsTitle}>Contents</Text>}
        <View style={styles.content}>
          {contentItems
            .slice(pageIndex * ITEMS_PER_PAGE, (pageIndex + 1) * ITEMS_PER_PAGE)
            .map((item, itemIndex) => (
              <React.Fragment key={itemIndex}>
                {React.isValidElement(item) && item.type === Link ? (
                  React.cloneElement(item, { style: styles.link })
                ) : (
                  <Text style={styles.contentItem}>{item}</Text>
                )}
              </React.Fragment>
            ))}
        </View>
        {pageIndex < pageCount - 1 && (
          <Text style={styles.pageNumber}>Continued on next page...</Text>
        )}
      </View>
    </Page>
  ));
};

const renderContents = (graph) => { //render each person in contents
  const allPeople = new Set();

  const renderContentsItem = (id) => {
    if (allPeople.has(id)) {
      return null;
    }
    allPeople.add(id);

    const person = graph.nodes.find(node => node.id === id);
    if (!person) return null;

    const birthYear = person.data.birth_date ? new Date(person.data.birth_date).getFullYear() : null;
    const deathYear = person.data.death_date ? new Date(person.data.death_date).getFullYear() : null;

    let yearInfo = '';
    if (birthYear && deathYear) {
      yearInfo = ` (${birthYear}-${deathYear})`;
    } else if (birthYear) {
      yearInfo = ` (b. ${birthYear})`;
    } else if (deathYear) {
      yearInfo = ` (d. ${deathYear})`;
    }

    return (
      <React.Fragment key={person.id}>
        <Text style={styles.contentItem}>
          <Link src={`#${person.id}-0`} style={styles.link}>
            {person.data.name}
          </Link>
          {yearInfo}
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
  const month = date.toLocaleString('default', { month: 'long', timeZone: 'UTC' }); //UTC required as local time conversion shifts a day back
  const year = date.getUTCFullYear();
  
  if (day === 1 && date.getUTCMonth() === 0) {
    return `${year}`;
  } else if (day === 1) {
    return `${month} ${year}`;
  } else {
    return `${day} ${month} ${year}`;
  }
};

const EVENTS_PER_PAGE = 20;

const TimelinePage = ({ person, events }) => {
  const pageCount = Math.ceil(events.length / EVENTS_PER_PAGE);

  return Array.from({ length: pageCount }, (_, pageIndex) => (
    <Page key={`timeline-${pageIndex}`} size="A5" style={styles.timelinePage}>
      <View style={styles.border}>
        <Text style={styles.timelineTitle}>
          {person.data.name}'s Timeline {pageIndex > 0 ? `(continued)` : ''}
        </Text>
        <View style={styles.timelineContainer}>
          <View style={styles.timelineLine} />
          {events
            .slice(pageIndex * EVENTS_PER_PAGE, (pageIndex + 1) * EVENTS_PER_PAGE)
            .map((event, index) => (
              <View key={index} style={styles.timelineEvent}>
                <Text style={styles.timelineDate}>{event.date || 'Unknown date'}</Text>
                <Text style={styles.timelineDescription}>{event.description}</Text>
              </View>
            ))}
        </View>
        {pageIndex < pageCount - 1 && (
          <Text style={styles.pageNumber}>Continued on next page...</Text>
        )}
      </View>
    </Page>
  ));
};

const MAX_CHARS_PER_PAGE = 750;

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
    let bio = '';
    
    const birthDate = formatDate(data.birth_date);
    const hasKnownParents = parents.length > 0;
  
    if (!birthDate && !hasKnownParents) {
      bio += `${data.name}'s date of birth is unknown. `;
    } else if (!birthDate && hasKnownParents) {
      bio += `${data.name}'s date of birth is unknown, born to parents ${parents.map(p => p.name).join(' and ')}. `;
    } else if (birthDate && !hasKnownParents) {
      bio += `${data.name} was born ${birthDate.includes(' ') ? 'on' : 'in'} ${birthDate}. `;
    } else if (birthDate && hasKnownParents) {
      bio += `${data.name} was born ${birthDate.includes(' ') ? 'on' : 'in'} ${birthDate} to parents ${parents.map(p => p.name).join(' and ')}. `;
    }
  
    if (spouses.length > 0) {
      bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} `;
      if (spouses.length === 1) {
        const spouse = spouses[0];
        bio += `married ${spouse.data.name}`;
        if (spouse.marriageDate) {
          bio += ` ${spouse.marriageDate.includes(' ') ? 'on' : 'in'} ${spouse.marriageDate}`;
        }
        if (spouse.divorceDate && !spouse.isCurrent) {
          bio += ` and divorced ${spouse.divorceDate.includes(' ') ? 'on' : 'in'} ${spouse.divorceDate}`;
        }
        bio += '. ';
      } else {
        bio += 'married ';
        spouses.forEach((spouse, index) => {
          if (index > 0) bio += index === spouses.length - 1 ? ' and ' : ', ';
          bio += spouse.data.name;
          if (spouse.marriageDate) {
            bio += ` ${spouse.marriageDate.includes(' ') ? 'on' : 'in'} ${spouse.marriageDate}`;
          }
          if (spouse.divorceDate && !spouse.isCurrent) {
            bio += ` (divorced ${spouse.divorceDate.includes(' ') ? 'on' : 'in'} ${spouse.divorceDate})`;
          }
          if (spouse.isCurrent) {
            bio += ' (current spouse)';
          }
        });
        bio += '. ';
      }
    }
  
    if (children.length > 0) {
      bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had ${children.length} ${children.length === 1 ? 'child' : 'children'}: `;
      bio += children.map(child => child.data.name).join(', ') + '. ';
    }
  
    const deathDate = formatDate(data.death_date);
    if (deathDate) {
      bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away ${deathDate.includes(' ') ? 'on' : 'in'} ${deathDate}.`;
    }
  
    return bio;
  };

  const splitBiographyIntoPages = (biography) => {
    const pages = [];
    let currentPage = '';
    
    for (const char of biography) {
      if (currentPage.length >= MAX_CHARS_PER_PAGE) {
        pages.push(currentPage);
        currentPage = '';
      }
      currentPage += char;
    }

    if (currentPage.length > 0) {
      pages.push(currentPage);
    }

    return pages;
  };

  const generateTimeline = () => {
    const events = [];

    if (data.birth_date) {
      events.push({ date: formatDate(data.birth_date), description: 'Born' });
    }

    spouses.forEach((spouse, index) => {
      if (spouse.marriageDate) {
        events.push({ 
          date: spouse.marriageDate, 
          description: `Married ${spouse.data.name}` 
        });
      }
      if (spouse.divorceDate && !spouse.isCurrent) {
        events.push({ 
          date: spouse.divorceDate, 
          description: `Divorced from ${spouse.data.name}` 
        });
      }
    });

    children.forEach(child => {
      if (child.data.birth_date) {
        events.push({ 
          date: formatDate(child.data.birth_date), 
          description: `${child.data.name} born` 
        });
      }
    });

    if (data.death_date) {
      events.push({ date: formatDate(data.death_date), description: 'Passed away' });
    }

    events.sort((a, b) =>  {
      if (a.description === 'Born') return -1; //ensure born is always first
      if (b.description === 'Born') return 1;
      if (a.description === 'Passed away') return 1; //ensure passed away is always last
      if (b.description === 'Passed away') return -1;
      return new Date(a.date) - new Date(b.date)
    });

    return events;
  };

  const biography = generateBiography();
  const biographyPages = splitBiographyIntoPages(biography);
  const timelineEvents = generateTimeline();

  
  return (
    <>
      {biographyPages.map((pageContent, pageIndex) => (
        <Page key={`person-${person.id}-page-${pageIndex}`} size="A5" style={styles.page} id={`${person.id}-0`}>
          <View style={styles.border}>
            <Text style={styles.name}>{data.name}</Text>
            {data.image ? (
              <Image src={data.image} style={styles.photo} />
            ) : (
              <View style={styles.photoPlaceholder}>
                <Text style={styles.photoText}>Photo</Text>
              </View>
            )}
            <Text style={styles.biographyTitle}>Biography {pageIndex > 0 ? `(continued)` : ''}</Text>
            <View style={styles.biographyBox}>
              <Text style={styles.biographyText}>
                {pageContent}
              </Text>
            </View>
            {pageIndex < biographyPages.length - 1 && (
              <Text style={styles.pageNumber}>Continued on next page</Text>
            )}
        </View>
      </Page>
      ))}
      <TimelinePage person={person} events={timelineEvents} />
    </>
  );
};

const FamilyTreePDF = ({ onClose }) => { //fetch family data from backend
  const [familyData, setFamilyData] = useState({ nodes: [], edges: [] });
  const [bookTitle, setBookTitle] = useState('');
  const [isFormSubmitted, setIsFormSubmitted] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axios.get('/api/family-graph-json');
        setFamilyData(response.data);
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    };

    fetchData();
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault();
    setIsFormSubmitted(true);
  };

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

  const formStyle = {
    backgroundColor: 'white',
    padding: '20px',
    borderRadius: '10px',
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'center',
  };

  const inputStyle = {
    margin: '10px 0',
    padding: '5px',
    width: '300px',
    fontFamily: '"Inika", serif',
  };

  const buttonStyle = {
    margin: '10px 0',
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
      {!isFormSubmitted ? (
        <form onSubmit={handleSubmit} style={formStyle}>
          <h2>Create Family Book</h2>
          <input
            type="text"
            value={bookTitle}
            onChange={(e) => setBookTitle(e.target.value)}
            placeholder="Enter the title for your family book"
            style={inputStyle}
            required
          />
          <button type="submit" style={buttonStyle}>Create PDF</button>
        </form>
      ) : (
      <PDFViewer width="80%" height="80%">
        <Document>
            <TitlePage title={bookTitle} />
            <ContentsPage graph={familyData} />
            {renderPages(familyData)}
          </Document>
        </PDFViewer>
      )}
    </div>
  );
};

export default FamilyTreePDF;