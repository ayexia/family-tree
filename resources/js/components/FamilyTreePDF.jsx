import React, { useState, useEffect } from 'react';
import { Document, Page, Text, View, StyleSheet, Font, PDFViewer, Link, Image } from '@react-pdf/renderer';
import axios from 'axios';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css'; 
import FamilyTreeDiagram from './FamilyTreeDiagram';

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
    color: 'blue',
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
    textAlign: 'right',
  },
  backToContents: {
    fontSize: 8,
    color: 'blue',
    textDecoration: 'underline',
    position: 'absolute',
    bottom: 10,
    left: 10,
  },
  pageContent: {
    flex: 1,
    display: 'flex',
    flexDirection: 'column',
  },
  generationHeading: {
    fontSize: 12,
    fontWeight: 'bold',
    marginTop: 15,
    marginBottom: 10,
    textDecoration: 'underline',
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

const ITEMS_PER_PAGE = 20;

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

const ContentsPage = ({ graph, selectedPeople }) => { //contents page - allows 20 items per page before breaking to new page
  const contentItems = renderContents(graph, selectedPeople).filter(item => item !== null);
  const pageCount = Math.ceil(contentItems.length / ITEMS_PER_PAGE);

  return Array.from({ length: pageCount }, (_, pageIndex) => (
    <Page key={`contents-${pageIndex}`} size="A5" style={styles.page} id={pageIndex === 0 ? "contents" : undefined}>
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

const renderContents = (graph, selectedPeople) => { //render each person in contents
  const calculateGeneration = (personId, visited = new Set()) => {
    if (visited.has(personId)) return 0;
    visited.add(personId);
    const parents = graph.edges
      .filter(edge => edge.target === personId && edge.label === 'Child')
      .map(edge => edge.source);
    if (parents.length === 0) return 0;
    return 1 + Math.max(...parents.map(parentId => calculateGeneration(parentId, visited)));
  };

  const processedNodes = graph.nodes
    .filter(node => selectedPeople.length === 0 || selectedPeople.includes(node.id))
    .map(person => {
      const generation = calculateGeneration(person.id);
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

      return { ...person, generation, yearInfo };
    });

  const groupedByGeneration = processedNodes.reduce((generationGroup, person) => {
    if (!generationGroup[person.generation]) {
      generationGroup[person.generation] = [];
    }
    generationGroup[person.generation].push(person);
    return generationGroup;
  }, {});

  Object.values(groupedByGeneration).forEach(group => {
    group.sort((a, b) => {
      const dateA = a.data.birth_date ? new Date(a.data.birth_date) : new Date(9999, 11, 31);
      const dateB = b.data.birth_date ? new Date(b.data.birth_date) : new Date(9999, 11, 31);
      return dateA - dateB;
    });
  });

  return Object.entries(groupedByGeneration).flatMap(([generation, people]) => [
    <Text key={`gen-${generation}`} style={styles.generationHeading}>
      Generation {parseInt(generation) + 1}
    </Text>,
    ...people.map(person => (
      <Text key={person.id} style={styles.contentItem}>
        <Link src={`#${person.id}`} style={styles.link}>
          {person.data.name}
        </Link>
        {person.yearInfo} (<PersonInitials person={person} />)
      </Text>
    ))
  ]);
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

const TimelinePage = ({ person, events, selectedPeople }) => {
  const filteredEvents = events.filter(event => 
    event.relatedPersonId ? selectedPeople.includes(event.relatedPersonId) : true
  );
  const pageCount = Math.ceil(filteredEvents.length / EVENTS_PER_PAGE);

  return Array.from({ length: pageCount }, (_, pageIndex) => (
    <Page key={`timeline-${pageIndex}`} size="A5" style={styles.timelinePage}>
      <View style={styles.pageContent}>
        <View style={styles.border}>
          <Text style={styles.timelineTitle}>
            {person.data.name}'s Timeline {pageIndex > 0 ? `(continued)` : ''}
          </Text>
          <View style={styles.timelineContainer}>
            <View style={styles.timelineLine} />
            {filteredEvents
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
        <Link src="#contents" style={styles.backToContents}>
          Back to Contents
        </Link>
      </View>
    </Page>
  ));
};

const MAX_CHARS_PER_PAGE = 750;

const PersonPage = ({ person, graph, biographyLevel, selectedPeople }) => {
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
      return spouseNode ? {
        ...spouseNode,
        marriageDate: data.marriage_dates && data.marriage_dates[index] ? formatDate(data.marriage_dates[index]) : null,
        divorceDate: data.divorce_dates && data.divorce_dates[index] ? formatDate(data.divorce_dates[index]) : null,
        isCurrent: edge.is_current
      } : null;
    }).filter(Boolean); //filters null spouses

  const children = graph.edges
    .filter(edge => edge.source === person.id && edge.label === 'Child')
    .map(edge => graph.nodes.find(node => node.id === edge.target))
    .filter(Boolean); //filters null children

  const parents = data.parents && typeof data.parents === 'object' ? Object.values(data.parents).filter(Boolean) : [];

  const generateBiography = () => {
    let bio = '';
    let deathInfo = '';
    
    const birthDate = formatDate(data.birth_date);
    const deathDate = formatDate(data.death_date);
    
    if (deathDate && data.death_place) {
      deathInfo += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away ${deathDate.includes(' ') ? 'on' : 'in'} ${deathDate} in ${data.death_place}. `;
    } else if (deathDate) {
      deathInfo += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away ${deathDate.includes(' ') ? 'on' : 'in'} ${deathDate}. `;
    } else if (data.death_place) {
      deathInfo += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away in ${data.death_place}. `;
    }  

    if (birthDate && data.birth_place) {
      bio += `${data.name} was born on ${birthDate} in ${data.birth_place}. `;
    } else if (birthDate) {
      bio += `${data.name} was born on ${birthDate}. `;
    } else if (data.birth_place) {
      bio += `${data.name} was born in ${data.birth_place}. `;
    }

    const selectedParents = parents.filter(parent => selectedPeople.includes(parent.id));
    if (selectedParents.length > 0) {
      bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} parents were ${selectedParents.map(p => p.name).join(' and ')}. `;
    }

    const selectedSpouses = spouses.filter(spouse => selectedPeople.includes(spouse.id));
    if (selectedSpouses.length > 0) {
      bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} `;
      if (selectedSpouses.length === 1) {
        const spouse = selectedSpouses[0];
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
        selectedSpouses.forEach((spouse, index) => {
          if (index > 0) bio += index === selectedSpouses.length - 1 ? ' and ' : ', ';
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
  
    const selectedChildren = children.filter(child => selectedPeople.includes(child.id));
    if (selectedChildren.length > 0) {
      bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had ${selectedChildren.length} ${selectedChildren.length === 1 ? 'child' : 'children'}: `;
      const adoptedChildren = selectedChildren.map(child => {
          const adoptionInfo = child.data.isAdopted ? ' (adopted)' : '';
          return child.data.name + adoptionInfo;
      });
      if (selectedChildren.length === 1) {
          bio += adoptedChildren[0] + '. ';
      } else if (selectedChildren.length === 2) {
          bio += `${adoptedChildren[0]} and ${adoptedChildren[1]}. `;
      } else {
          bio += adoptedChildren.slice(0, -1).join(', ') + ', and ' + adoptedChildren[adoptedChildren.length - 1] + '. ';
      }
    }

    if (biographyLevel === 'comprehensive' || biographyLevel === 'detailed') {
      const selectedGrandparents = parents.flatMap(parent => 
        (parent.parents && typeof parent.parents === 'object') 
          ? Object.values(parent.parents).filter(gp => selectedPeople.includes(gp.id))
          : []
      );
      if (selectedGrandparents.length > 0) {
        bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} grandparents were `;
        if (selectedGrandparents.length === 1) {
          bio += `${selectedGrandparents[0].name}. `;
        } else if (selectedGrandparents.length === 2) {
          bio += `${selectedGrandparents[0].name} and ${selectedGrandparents[1].name}. `;
        } else {
          bio += selectedGrandparents.slice(0, -1).map(gp => gp.name).join(', ') + ', and ' + selectedGrandparents[selectedGrandparents.length - 1].name + '. ';
        }
      }

      const selectedGrandchildren = selectedChildren.flatMap(child => 
        graph.edges
          .filter(edge => edge.source === child.id && edge.label === 'Child')
          .map(edge => graph.nodes.find(node => node.id === edge.target))
          .filter(gc => selectedPeople.includes(gc.id))
      );
      if (selectedGrandchildren.length > 0) {
        bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} grandchildren are `;
        if (selectedGrandchildren.length === 1) {
          bio += `${selectedGrandchildren[0].data.name}. `;
        } else if (selectedGrandchildren.length === 2) {
          bio += `${selectedGrandchildren[0].data.name} and ${selectedGrandchildren[1].data.name}. `;
        } else {
          bio += selectedGrandchildren.slice(0, -1).map(gc => gc.data.name).join(', ') + ', and ' + selectedGrandchildren[selectedGrandchildren.length - 1].data.name + '. ';
        }
      }
    }

    if (biographyLevel === 'detailed') {
      if (data.pets && data.pets.length > 0) {
        if (data.pets.length === 1) {
          bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had a pet: ${data.pets[0]}. `;
        } else {
          bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had the following pets: ${data.pets.slice(0, -1).join(', ')}, and ${data.pets[data.pets.length - 1]}. `;
        }
      }

      if (data.hobbies && data.hobbies.length > 0) {
        if (data.hobbies.length === 1) {
          bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} hobby was ${data.hobbies[0]}. `;
        } else {
          bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} hobbies included ${data.hobbies.slice(0, -1).join(', ')}, and ${data.hobbies[data.hobbies.length - 1]}. `;
        }
      }
    }

    bio += deathInfo;
    
    if (data.notes) {
      const notes = Array.isArray(data.notes) ? data.notes : [data.notes];
      if (notes.length === 1) {
        bio += `Additional note: ${notes[0]} `;
      } else if (notes.length > 1) {
        bio += `Additional notes: ${notes.join(' ')} `;
      }
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
      if (selectedPeople.includes(spouse.id)) {
        if (spouse.marriageDate) {
          events.push({ 
            date: spouse.marriageDate, 
            description: `Married ${spouse.data.name}`,
            relatedPersonId: spouse.id
          });
        }
        if (spouse.divorceDate && !spouse.isCurrent) {
          events.push({ 
            date: spouse.divorceDate, 
            description: `Divorced from ${spouse.data.name}`,
            relatedPersonId: spouse.id
          });
        }
      }
    });

    children.forEach(child => {
      if (selectedPeople.includes(child.id) && child.data.birth_date) {
        events.push({ 
          date: formatDate(child.data.birth_date), 
          description: `${child.data.name} born`,
          relatedPersonId: child.id
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
      <Page key={`person-${person.id}-page-${pageIndex}`} size="A5" style={styles.page} id={pageIndex === 0 ? `${person.id}` : `${person.id}-${pageIndex}`}>
        <View style={styles.pageContent}>
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
              <Text style={styles.pageNumber}>Continued on next page...</Text>
            )}
          </View>
          <Link src="#contents" style={styles.backToContents}>
            Back to Contents
          </Link>
        </View>
      </Page>
      ))}
      <TimelinePage person={person} events={timelineEvents} selectedPeople={selectedPeople} />
    </>
  );
};

const FamilyTreePDF = ({ onClose }) => { //fetch family data from backend
  const [familyData, setFamilyData] = useState({ nodes: [], edges: [] });
  const [bookTitle, setBookTitle] = useState('');
  const [selectedPeople, setSelectedPeople] = useState([]);
  const [biographyLevel, setBiographyLevel] = useState('basic');
  const [isFormSubmitted, setIsFormSubmitted] = useState(false);
  const [availablePeople, setAvailablePeople] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axios.get('/api/family-graph-json');
        setFamilyData(response.data);
        setAvailablePeople(response.data.nodes.map(node => node.id));
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

  const handlePersonSelection = (personId) => {
    setSelectedPeople(prev => 
      prev.includes(personId) 
        ? prev.filter(id => id !== personId)
        : [...prev, personId]
    );
  };

  const handleSelectAll = (e) => {
    if (e.target.checked) {
      setSelectedPeople(availablePeople);
    } else {
      setSelectedPeople([]);
    }
  };

  const handleBiographyLevelChange = (e) => {
    setBiographyLevel(e.target.value);
  };

  const renderPages = (graph) => {
    return [
      <FamilyTreeDiagram key="family-tree" selectedPeople={selectedPeople} graph={graph} />,
      ...selectedPeople.map(id => {
        const person = graph.nodes.find(node => node.id === id);
        return person ? (
          <PersonPage 
            key={`${person.id}-${biographyLevel}`} 
            person={person} 
            graph={graph} 
            biographyLevel={biographyLevel} 
            selectedPeople={selectedPeople}
          />
        ) : null;
      }).filter(Boolean)
    ];
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
    left: '270px',
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
    <Tippy content="Close the PDF viewer">
      <button style={closeButton} onClick={onClose}>Close</button>
    </Tippy>
    <div style={{ display: 'flex', width: '100%', height: '100%' }}>
      <div style={{ width: '300px', backgroundColor: 'white', padding: '20px', overflowY: 'auto' }}>
        <h2>Create Family Book</h2>
        <form onSubmit={handleSubmit} style={formStyle}>
          <input
            type="text"
            value={bookTitle}
            onChange={(e) => setBookTitle(e.target.value)}
            placeholder="Enter the title for your family book"
            style={inputStyle}
            required
          />
        <Tippy content="Choose the level of detail for each person's biography">
          <select
            value={biographyLevel}
            onChange={handleBiographyLevelChange}
            style={inputStyle}
          >
          <option value="basic">Basic (Parents, Spouses, Children)</option>
          <option value="comprehensive">Comprehensive (Including Grandparents and Grandchildren)</option>
          <option value="detailed">Detailed (Including Pets, Hobbies, and Notes)</option>
          </select>
        </Tippy>
        <h3>Select People to Include:</h3>
      <div style={{ maxHeight: '200px', overflowY: 'auto' }}>
          <div>
          <Tippy content="Select or deselect all people">
            <input
              type="checkbox"
              id="select-all"
              checked={selectedPeople.length === availablePeople.length}
              onChange={handleSelectAll}
            />
          </Tippy>
            <label htmlFor="select-all">Select All</label>
          </div>
          {availablePeople.map(id => {
            const node = familyData.nodes.find(node => node.id === id);
            return (
              <div key={id}>
              <Tippy content={`Include or exclude ${node.data.name} from the book`}>
                <input
                  type="checkbox"
                  id={`person-${id}`}
                  checked={selectedPeople.includes(id)}
                  onChange={() => handlePersonSelection(id)}
                />
                </Tippy>
                <label htmlFor={`person-${id}`}>{node.data.name}</label>
              </div>
              );
            })}
          </div>
        <Tippy content="Generate the PDF with the selected options">
          <button type="submit" style={buttonStyle}>Create PDF</button>
        </Tippy>
        </form>
      </div>
        <div style={{ flex: 1 }}>
          {isFormSubmitted && (
            <PDFViewer width="100%" height="100%">
              <Document>
                <TitlePage title={bookTitle} />
                <ContentsPage graph={familyData} selectedPeople={selectedPeople} />
                {renderPages(familyData)}
              </Document>
            </PDFViewer>
          )}
        </div>
      </div>
    </div>
  );
};

export default FamilyTreePDF;