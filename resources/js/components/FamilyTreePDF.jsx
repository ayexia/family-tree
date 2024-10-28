import React, { useState, useEffect } from 'react'; // import react and necessary hooks
import { Document, Page, Text, View, StyleSheet, Font, PDFViewer, Link, Image } from '@react-pdf/renderer'; // import components for pdf rendering - https://github.com/diegomura/react-pdf 
import axios from 'axios'; // import axios making http requests 
import Tippy from '@tippyjs/react'; // import tippy for tooltips - https://github.com/atomiks/tippyjs
import 'tippy.js/dist/tippy.css';  // import tippy css for styling
import FamilyTreeDiagram from './FamilyTreeDiagram'; // import family tree diagram component 

// register custom fonts for pdf use
Font.register({
  family: 'Great Vibes', // define font family 'great vibes'
  src: "/fonts/GreatVibes-Regular.ttf", // specify font file source
});

Font.register({
  family: 'Corben', // define font family 'corben'
  src: "/fonts/Corben-Regular.ttf", // specify font file source
});

// define styles for pdf doc
const styles = StyleSheet.create({  // css for styling
  page: {
    padding: 30, // set padding around page content
    backgroundColor: '#ffffff', // set background colour white
    fontFamily: 'Corben', // use 'corben' as default font family
  },
  titlePage: {  // flexbox layout centring title page content
    display: 'flex',
    flexDirection: 'column', // stack items vertically
    justifyContent: 'center', // centre items vertically 
    alignItems: 'center', // centre items horizontally 
    height: '100%', // full height of container 
    width: '100%', // full width of container
  },
  title: {
    fontSize: 30, // size of title font
    fontFamily: 'Great Vibes', // elegant cursive font
    fontWeight: 'bold', // bold style for emphasis
    textAlign: 'center', // centre text alignment
    marginTop: 200, // space above title
  },
  content: {
    marginLeft: 30, // left margin content
  },
  contentsTitle: {
    fontSize: 28, // size of contents title font
    fontFamily: 'Great Vibes', // same font consistency 
    marginBottom: 20, // space below contents title
    textAlign: 'center', // centre text alignment 
  },
  contentItem: {
    marginBottom: 5, // space below each content item
    fontSize: 8, // small font size for items
  },
  link: {
    color: 'blue', // blue colour for links
    textDecoration: 'underline', // underline for emphasis 
  },
  border: {
    border: '1pt solid black', // thin black border
    width: '100%', // full width container
    height: '100%', // full height container
    padding: 30, // padding inside border
    boxSizing: 'border-box', // include padding in width/height calculations 
    display: 'flex', // use flexbox layout 
    flexDirection: 'column', // stack items vertically 
  },
  name: {
    fontSize: 28, // size of name font
    fontFamily: 'Great Vibes', // consistent font style
    marginBottom: 20, // space below name
    textAlign: 'center', // centre text alignment 
  },
  photoPlaceholder: {
    width: 150, // full width placeholder
    height: 150, // full height placeholder
    borderRadius: 75, // circular shape placeholder
    backgroundColor: '#E0E0E0', // light grey background
    display: 'flex', // flexbox centring content
    justifyContent: 'center', // centre items horizontally 
    alignItems: 'center', // centre items vertically 
    marginBottom: 20, // space below placeholder
    alignSelf: 'center', // centre placeholder in container
  },
  photo: {
    width: 150, // set width of photo 
    height: 150, // set height of photo
    borderRadius: 75, // make photo circular
    marginBottom: 20, // space below photo
    alignSelf: 'center', // centre photo horizontally 
  },
  photoText: {
    fontSize: 12, // set font size caption
    color: '#666666', // grey colour for text
  },
  biographyTitle: {
    fontSize: 20, // larger font title
    fontFamily: 'Great Vibes', // use decorative font
    marginBottom: 5, // space below title
    textAlign: 'left', // align text to left
  },
  biographyBox: {
    border: '1pt solid black', // thin black border
    padding: 10, // padding inside box
    marginTop: 'auto', // pushes box to bottom
    flexGrow: 1, // allows box to expand
  },
  biographyText: {
    fontSize: 9, // smaller font size for content
    lineHeight: 1.5, // spacing below line
    wordBreak: 'break-word', // break words if too long
  },
  timelinePage: {
    padding: 40, // space around page
    backgroundColor: '#ffffff', // white background
    fontFamily: 'Corben', // use specific font for text
  },
  timelineTitle: {
    fontSize: 20, // set font size for title
    fontFamily: 'Great Vibes', // use decorative font for title
    marginBottom: 30, // space below title
    textAlign: 'center', // centre text alignment
  },
  bulletPoint: {
    width: 5, // width bullet point
    height: 5, // height bullet point
    borderRadius: 5, // make bullet point circular
    backgroundColor: 'black', // colour of bullet point
    position: 'absolute', // position it absolutely 
    left: 0, // align to left
    zIndex: 1, // ensure it appears above other elements
  },
  timelineEventContainer: {
    flexDirection: 'row', // align items in row
    alignItems: 'center', // centre items vertically 
  },
  timelineLine: {
    position: 'absolute', // position absolutely 
    left: 2.5, // offset from left
    top: 0, // start at top
    bottom: 0, // extend to bottom 
    width: 1, // thin line width
    backgroundColor: 'black', // line colour
  },
  timelineEvent: {
    flexDirection: 'row', // align items below
    marginBottom: 5, // space below each event
    alignItems: 'center', // centre items vertically 
  },
  timelineDate: {
    width: '30%', // allocate 30% width for date
    fontSize: 8, // smaller font size for date 
    textAlign: 'right', // align date to right
    paddingRight: 5, // space on right
  },
  timelineDescription: {
    width: '70%', // allocate 70% width for description 
    fontSize: 8, // smaller font size
    paddingLeft: 5, // space on left 
  },
  pageNumber: {
    fontSize: 8, // smaller font size
    textAlign: 'right', // align page number to right
  },
  backToContents: {
    fontSize: 8, // smaller font size for link
    color: 'blue', // colour for link
    textDecoration: 'underline', // underline text
    position: 'absolute', // position it absolutely 
    bottom: 10, // offset from bottom 
    left: 10, // offset from left
  },
  pageContent: {
    flex: 1, // allow growth and fill space
    display: 'flex', // use flexbox for layout
    flexDirection: 'column', // arrange items in column 
  },
  generationHeading: {
    fontSize: 12, // set font size for heading
    fontWeight: 'bold', // make heading bold
    marginTop: 15, // space above heading
    marginBottom: 10, // space below heading
    textDecoration: 'underline', // underline heading
  },
 });
 
 const TitlePage = ({ title }) => (  // title page component 
  <Page size="A5" style={styles.page}> {/* defines page size and style */}
    <View style={styles.titlePage}> {/* view container for title page */}
      <View style={styles.border}> {/* border around title for emphasis */}
        <Text style={styles.title}>{title}</Text> {/* displays title passed as prop */}
      </View>
    </View>
  </Page>
 );
 
 const ITEMS_PER_PAGE = 20; // constant defining number of items per page
 
 const getInitials = (name) => { // function to extract initials from given name
  return name
    .split(' ') // splits name into array of words
    .map(n => n[0]) // maps each word to its first character
    .join('') // joins the initials into single string
    .toUpperCase(); // converts initials to uppercase
 };
 
 const PersonInitials = ({ person }) => ( // define functional component for displaying person's initials 
  <Text style={styles.personInitials}>{getInitials(person.data.name)}</Text>
 );

 const ContentsPage = ({ graph, selectedPeople }) => { // contents page allows 20 items per page before breaking to new page
 
 const contentItems = renderContents(graph, selectedPeople).filter(item => item !== null); // render content items from graph and filter out null items
 const pageCount = Math.ceil(contentItems.length / ITEMS_PER_PAGE); // calculate number of pages required based on total items and items per page  

 return Array.from({ length: pageCount }, (_, pageIndex) => (
   <Page key={`contents-${pageIndex}`} size="A5" style={styles.page} id={pageIndex === 0 ? "contents" : undefined}>
     {/* each page is defined with unique key and size */}
     <View style={styles.border}>
       {/* render a title only on first page */}
       {pageIndex === 0 && <Text style={styles.contentsTitle}>Contents</Text>}
       <View style={styles.content}>
         {/* slice content items for current page */}
         {contentItems
           .slice(pageIndex * ITEMS_PER_PAGE, (pageIndex + 1) * ITEMS_PER_PAGE)
           .map((item, itemIndex) => (
             // map through sliced items to render them 
             <React.Fragment key={itemIndex}>
               {/* check item is valid react element and if it's type is link */}
               {React.isValidElement(item) && item.type === Link ? (
                 // clone link element apply custom styles
                 React.cloneElement(item, { style: styles.link })
               ) : (
                 // if not link render item as text component
                 <Text style={styles.contentItem}>{item}</Text>
               )}
             </React.Fragment>
           ))}
       </View>
       {/* check if there's another page to display, show continuation message if so */}
       {pageIndex < pageCount - 1 && (
         <Text style={styles.pageNumber}>Continued on next page...</Text>
       )}
     </View>
   </Page>
 ));
};

const renderContents = (graph, selectedPeople) => {  //render each person in contents
  const calculateGeneration = (personId, visited = new Set()) => {
 //function to calculate generation of person based on id
    if (visited.has(personId)) return 0; //return 0 if person been visited
    visited.add(personId); //mark person as visited
    const parents = graph.edges
      .filter(edge => edge.target === personId && edge.label === 'Child') //find parents of current person
      .map(edge => edge.source); //extract parent ids
    if (parents.length === 0) return 0; //return 0 if no parents
    return 1 + Math.max(...parents.map(parentId => calculateGeneration(parentId, visited))); //calculate generation recursively 
  };

  const processedNodes = graph.nodes
    .filter(node => selectedPeople.length === 0 || selectedPeople.includes(node.id)) //filter nodes based on selected people
    .map(person => {
      const generation = calculateGeneration(person.id); //calculate generation for each person
      const birthYear = person.data.birth_date ? new Date(person.data.birth_date).getFullYear() : null;  //get birth year if available 
      const deathYear = person.data.death_date ? new Date(person.data.death_date).getFullYear() : null; //get death year if available 

      let yearInfo = '';  //initialise year info variable
      if (birthYear && deathYear) {
        yearInfo = ` (${birthYear}-${deathYear})`; //format year range if both years present
      } else if (birthYear) {
        yearInfo = ` (b. ${birthYear})`; //format birth year if present
      } else if (deathYear) {
        yearInfo = ` (d. ${deathYear})`; //format death year if present
      }

      return { ...person, generation, yearInfo }; //return person object with added generation and year info
    });

    const groupedByGeneration = processedNodes.reduce((acc, person) => {
      // group processed nodes by generation    
      if (!acc[person.generation]) {
        acc[person.generation] = [];  // initialise array if generation doesn't exist
      }
      acc[person.generation].push(person); // add person to appropriate generation 
      return acc; // return accumulator
    }, {});
   
    // sort each group of people alphabetical order by name
    Object.values(groupedByGeneration).forEach(group => {
      group.sort((a, b) => a.data.name.localeCompare(b.data.name));
    });
   
    // return array of entries from grouped by generation object
    return Object.entries(groupedByGeneration)
      .sort(([genA], [genB]) => parseInt(genA) - parseInt(genB)) // sort generation numerically
      .flatMap(([generation, people]) => [ // flatten array into single array components
        <Text key={`gen-${generation}`} style={styles.generationHeading}>
          Generation {parseInt(generation) + 1}
        </Text>,
        ...people.map(person => ( // map over each person in current generation
          <Text key={person.id} style={styles.contentItem}>
            <Link src={`#${person.id}`} style={styles.link}>
              {person.data.name}
            </Link>
            {person.yearInfo} ({getInitials(person.data.name)}) {/* display year info and initials */}
          </Text>
        ))
      ]);
   };
   
   const formatDate = (dateString) => {
    if (!dateString) return null; // return null if no date string provided
    const date = new Date(dateString); // create date object from provided date string
    if (isNaN(date.getTime())) return null; // check date is valid return null if not
  
    const day = date.getUTCDate(); // get day of month in utc
    const month = date.toLocaleString('default', { month: 'long', timeZone: 'UTC' });  // get month name in utc using 'default' locale ensuring consistency
    const year = date.getUTCFullYear(); // get year utc
    
    // based on specific conditions format date
    if (day === 1 && date.getUTCMonth() === 0) {
      return `${year}`; // if 1st jan return only year
    } else if (day === 1) {
      return `${month} ${year}`; // if 1st of any month return month and year
    } else {
      return `${day} ${month} ${year}`; // all other days return day, month, year
    }
   };
   
   const EVENTS_PER_PAGE = 20;  // define number of events to display per page
   
   const TimelinePage = ({ person, events, selectedPeople }) => {
    // filter events based on selected people, showing all if none selected
    const filteredEvents = events.filter(event => 
      event.relatedPersonId ? selectedPeople.includes(event.relatedPersonId) : true
    );
    const pageCount = Math.ceil(filteredEvents.length / EVENTS_PER_PAGE); // calculate total number of pages for filtered events
   
    // create an array for each page based on calculated page count
    return Array.from({ length: pageCount }, (_, pageIndex) => (
      <Page key={`timeline-${pageIndex}`} size="A5" style={styles.timelinePage}>
        <View style={styles.pageContent}>
          <View style={styles.border}>
            <Text style={styles.timelineTitle}>
              {person.data.name}'s Timeline {pageIndex > 0 ? `(continued)` : ''} {/* show person's name indicate if it's a continuation */}
            </Text>
            <View>
           <View style={styles.timelineLine} /> {/* add visual line to separate events */}
           {filteredEvents
             .slice(pageIndex * EVENTS_PER_PAGE, (pageIndex + 1) * EVENTS_PER_PAGE) // select events for current page
             .map((event, index) => (
             <View key={index} style={styles.timelineEventContainer}>
               <View style={styles.bulletPoint} /> {/* display bullet point for each event */}
               <View style={styles.timelineEvent}>
                 <Text style={styles.timelineDate}>{event.date || 'Unknown date'}</Text> {/* show date or default to 'unknown date' */}
                 <Text style={styles.timelineDescription}>{event.description}</Text> {/* display event description */}
               </View>
             </View>
             ))}
         </View>
         {pageIndex < pageCount - 1 && ( // placeholder for additional content if not on last page
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

const MAX_CHARS_PER_PAGE = 750; // constant defining max number of characters per page

const PersonPage = ({ person, graph, biographyLevel, selectedPeople }) => {
 // check persons data is available
 if (!person || !person.data) {
   return (
     <Page size="A5" style={styles.page}>
       <View style={styles.border}>
         <Text style={styles.name}>Unknown Person</Text>
       </View>
     </Page>
   );
 }
 const { data } = person; // destructure data from person object

 const spouses = graph.edges
   .filter(edge => edge.source === person.id && edge.label === 'Spouse')  // select spouse edges
   .map((edge, index) => {
     const spouseNode = graph.nodes.find(node => node.id === edge.target); // find spouse node
     return spouseNode ? {
       ...spouseNode, // spread spouses data
       marriageDate: data.marriage_dates && data.marriage_dates[index] ? formatDate(data.marriage_dates[index]) : null, // format marriage date if exists
       divorceDate: data.divorce_dates && data.divorce_dates[index] ? formatDate(data.divorce_dates[index]) : null, // format divorce date if exists
       isCurrent: edge.is_current // indicate if marriage is current
     } : null;
   }).filter(Boolean);  // filters null spouses

 const children = graph.edges
   .filter(edge => edge.source === person.id && edge.label === 'Child')  // select child edges
   .map(edge => graph.nodes.find(node => node.id === edge.target)) // find child nodes 
   .filter(Boolean);  // filters null children

 // retrieve and validate parent info
 const parents = data.parents && typeof data.parents === 'object' ? Object.values(data.parents).filter(Boolean) : []; // extract parents ensuring they're valid

 // function to generate bio summary
 const generateBiography = () => {
   let bio = ''; // initialise bio string
   let deathInfo = '';  // initialise death info string
   
   const birthDate = formatDate(data.birth_date);  // format birth date
   const deathDate = formatDate(data.death_date); // format death date
    
 // construct death info based on available data   
 if (deathDate && data.death_place) {
  deathInfo += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away ${deathDate.includes(' ') ? 'on' : 'in'} ${deathDate} in ${data.death_place}. `;
} else if (deathDate) {
  deathInfo += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away ${deathDate.includes(' ') ? 'on' : 'in'} ${deathDate}. `;
} else if (data.death_place) {
  deathInfo += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} passed away in ${data.death_place}. `;
}  

// construct birth info based on available data   
if (birthDate && data.birth_place) {
  bio += `${data.name} was born on ${birthDate} in ${data.birth_place}. `;
} else if (birthDate) {
  bio += `${data.name} was born on ${birthDate}. `;
} else if (data.birth_place) {
  bio += `${data.name} was born in ${data.birth_place}. `;
}

// filter selected parents from parents array based on selected people
const selectedParents = parents.filter(parent => selectedPeople.includes(parent.id));

// check if there are any selected parents    
if (selectedParents.length > 0) {
  // append appropriate pronoun and parents names to bio     
  bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} parents were ${selectedParents.map(p => p.name).join(' and ')}. `;
}

 // filter selected spouses from the spouses array based on selected people    
 const selectedSpouses = spouses.filter(spouse => selectedPeople.includes(spouse.id));
   
 // check if any selected spouses   
 if (selectedSpouses.length > 0) {
   // append appropriate pronoun based on gender    
   bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} `;
   
   // handle case for single selected spouse      
   if (selectedSpouses.length === 1) {
     const spouse = selectedSpouses[0];
     // check if there's marriage date and append it        
     bio += `married ${spouse.data.name}`;
     if (spouse.marriageDate) {
       bio += ` ${spouse.marriageDate.includes(' ') ? 'on' : 'in'} ${spouse.marriageDate}`;
     }
     // if divorce date and spouse is not current append it       
     if (spouse.divorceDate && !spouse.isCurrent) {
       bio += ` and divorced ${spouse.divorceDate.includes(' ') ? 'on' : 'in'} ${spouse.divorceDate}`;
     }
     bio += '. '; // end the sentence
   } else {
     bio += 'married ';
     selectedSpouses.forEach((spouse, index) => {
       // add commas, and, for proper formatting          
       if (index > 0) bio += index === selectedSpouses.length - 1 ? ' and ' : ', ';
       bio += spouse.data.name;
       // check if there's marriage date and append it          
       if (spouse.marriageDate) {
         bio += ` ${spouse.marriageDate.includes(' ') ? 'on' : 'in'} ${spouse.marriageDate}`;
       }
       // if divorce date and spouse isn't current append it          
       if (spouse.divorceDate && !spouse.isCurrent) {
         bio += ` (divorced ${spouse.divorceDate.includes(' ') ? 'on' : 'in'} ${spouse.divorceDate})`;
       }
       // if spouse current note that as well          
       if (spouse.isCurrent) {
         bio += ' (current spouse)';
       }
     });
     bio += '. ';  // end sentence
   }
 }
// filter children array, get only whose ids are in selected people array
const selectedChildren = children.filter(child => selectedPeople.includes(child.id));
   
// check if any selected children    
if (selectedChildren.length > 0) {
  // construct initial part of bio on the datas gender      
  bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had ${selectedChildren.length} ${selectedChildren.length === 1 ? 'child' : 'children'}: `;
  
  // create array of adopted children's name adding adoption info if applicable     
  const adoptedChildren = selectedChildren.map(child => {
      const adoptionInfo = child.data.isAdopted ? ' (adopted)' : ''; // check if child's adopted
      return child.data.name + adoptionInfo; // return child's name with adoption info
  });
  
  // handle case for one selected child      
  if (selectedChildren.length === 1) {
      bio += adoptedChildren[0] + '. '; // append single child's name to bio
      
  // handle case for two selected children
  } else if (selectedChildren.length === 2) {
      bio += `${adoptedChildren[0]} and ${adoptedChildren[1]}. `;  // append both names to bio
  
  // handle the case for three or more selected children
  } else {
      bio += adoptedChildren.slice(0, -1).join(', ') + ', and ' + adoptedChildren[adoptedChildren.length - 1] + '. ';  // amend names with correct punctuation 
  }
}
if (biographyLevel === 'comprehensive' || biographyLevel === 'detailed') {
  // flatten parents array get selected grandparents      
  const selectedGrandparents = parents.flatMap(parent => 
    // check if parent has grandparents listed and if it's an object        
    (parent.parents && typeof parent.parents === 'object') 
      ? Object.values(parent.parents).filter(gp => selectedPeople.includes(gp.id)) // filter grandparents based on selected people
      : []
  );

  // proceed if grandparents      
  if (selectedGrandparents.length > 0) {
    // determine correct pronoun based on gender        
    bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} grandparents were `;
    
    // handle case with one grandparent        
    if (selectedGrandparents.length === 1) {
      bio += `${selectedGrandparents[0].name}. `;
    // handle case with two grandparents          
    } else if (selectedGrandparents.length === 2) {
      bio += `${selectedGrandparents[0].name} and ${selectedGrandparents[1].name}. `;
    // handle case with more than two grandparents        
    } else {
      bio += selectedGrandparents.slice(0, -1).map(gp => gp.name).join(', ') + ', and ' + selectedGrandparents[selectedGrandparents.length - 1].name + '. ';
    }
  }

  // create array of selected grandchildren by flattening selected children
  const selectedGrandchildren = selectedChildren.flatMap(child => 
    graph.edges
      // filter edges to find where child is source and label is 'child'          
      .filter(edge => edge.source === child.id && edge.label === 'Child')
      // map filtered edges to corresponding target nodes         
      .map(edge => graph.nodes.find(node => node.id === edge.target))
      // filter mapped nodes to only those present in selected people          
      .filter(gc => selectedPeople.includes(gc.id))
  );
 // check if there are any selected grandchildren      
 if (selectedGrandchildren.length > 0) {
  // determine correct pronoun based on gender        
  bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} grandchildren are `;
  
  // handle case where only one grandchild        
  if (selectedGrandchildren.length === 1) {
    bio += `${selectedGrandchildren[0].data.name}. `;
  // handle cases where there are two grandchildren       
  } else if (selectedGrandchildren.length === 2) {
    bio += `${selectedGrandchildren[0].data.name} and ${selectedGrandchildren[1].data.name}. `;
  // handle cases with three or more grandchildren       
  } else {
    bio += selectedGrandchildren.slice(0, -1).map(gc => gc.data.name).join(', ') + ', and ' + selectedGrandchildren[selectedGrandchildren.length - 1].data.name + '. ';
  }
}
}

    if (biographyLevel === 'detailed') { // check if bio level is detailed
    if (data.pets && data.pets.length > 0) { // verify any pets listed
      if (data.pets.length === 1) { // if there's only one pet
        bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had a pet: ${data.pets[0]}. `; // append sentence about pet
      } else { // if there are multiple pets
        bio += `${data.gender === 'M' ? 'He' : data.gender === 'F' ? 'She' : 'They'} had the following pets: ${data.pets.slice(0, -1).join(', ')}, and ${data.pets[data.pets.length - 1]}. `; // list all pets
      }
    }

    if (data.hobbies && data.hobbies.length > 0) { // check any hobbies listed
      if (data.hobbies.length === 1) { // if only one hobby
        bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} hobby was ${data.hobbies[0]}. `; // add single hobby
      } else { // if multiple hobbies
        bio += `${data.gender === 'M' ? 'His' : data.gender === 'F' ? 'Her' : 'Their'} hobbies included ${data.hobbies.slice(0, -1).join(', ')}, and ${data.hobbies[data.hobbies.length - 1]}. `; // list all hobbies
      }
    }
  }

  bio += deathInfo; // append death info to bio
  
  if (data.notes) { // check any notes present
    const notes = Array.isArray(data.notes) ? data.notes : [data.notes]; // ensure notes are in array format
    if (notes.length === 1) { // if only one note
      bio += `Additional note: ${notes[0]} `; // add single note to bio
    } else if (notes.length > 1) { // if multiple notes
      bio += `Additional notes: ${notes.join(' ')} `;  // concatenate and add all notes to the bio
    }
  }

  return bio; // return completed bio
};

const splitBiographyIntoPages = (biography) => { // function to divide bio into pages
  const pages = []; // initialise array to hold pages 
  let currentPage = ''; // start with empty current page
  
  for (const char of biography) { // loop through each character in bio
    if (currentPage.length >= MAX_CHARS_PER_PAGE) { // check if current page exceeds max character limit
      pages.push(currentPage); // add the current page to pages array
      currentPage = ''; // reset current page for new content
    }
    currentPage += char; // append character to current page
  }

  if (currentPage.length > 0) { // if remaining content in current page 
    pages.push(currentPage); // add to the pages array
  }

  return pages; // return the array of pages
};

const generateTimeline = () => { // function to generate a timeline of events
  const events = []; // initialise array to hold events

  if (data.birth_date) { // check birthdate is present
    events.push({ date: formatDate(data.birth_date), description: 'Born' }); // add birth event to timeline
  }
 // iterate over each spouse in spouses array
 spouses.forEach((spouse, index) => { 
  // check if spouse ID is in selected people array 
  if (selectedPeople.includes(spouse.id)) {
    // if marriage date push event for the marriage        
    if (spouse.marriageDate) {
      events.push({ 
        date: spouse.marriageDate, 
        description: `Married ${spouse.data.name}`, // description notes spouses name
        relatedPersonId: spouse.id  // link event to spouse id
      });
    }
    // if there's divorce date and not current a marriage push event for the divorce         
    if (spouse.divorceDate && !spouse.isCurrent) {
      events.push({ 
        date: spouse.divorceDate, 
        description: `Divorced from ${spouse.data.name}`, // description notes the ex spouses name
        relatedPersonId: spouse.id  // link event to spouse id
      });
    }
  }
});

// iterate over each child in children array
children.forEach(child => {
  // check if child id is in selected people array and if there's birth date      
  if (selectedPeople.includes(child.id) && child.data.birth_date) {
    events.push({ 
      date: formatDate(child.data.birth_date), // format birthdate for consistency 
      description: `${child.data.name} born`, // description includes child's name
      relatedPersonId: child.id  // link event to child id
    });
  }
});

// check death date for person
if (data.death_date) {
  events.push({ date: formatDate(data.death_date), description: 'Passed away' }); // add passing event
}

// sort events array for chronological order
events.sort((a, b) =>  {
  // ensure born events always first in sorted order      
  if (a.description === 'Born') return -1;  // ensure born is always first
  if (b.description === 'Born') return 1;
  // ensure passed away events always last in sorted order      
  if (a.description === 'Passed away') return 1;  // ensure passed away is always last
  if (b.description === 'Passed away') return -1;
  // sort remaining events by date      
  return new Date(a.date) - new Date(b.date)
});

return events; // return sorted list of events
};

const biography = generateBiography(); // generate bio for person
const biographyPages = splitBiographyIntoPages(biography); // split bio into pages
const timelineEvents = generateTimeline(); // generate timeline of events
return (
  <>
    {biographyPages.map((pageContent, pageIndex) => ( // map over each page of bio
      <Page key={`person-${person.id}-page-${pageIndex}`} size="A5" style={styles.page} id={pageIndex === 0 ? `${person.id}` : `${person.id}-${pageIndex}`}>
        <View style={styles.pageContent}> 
          <View style={styles.border}> 
            <Text style={styles.name}>{data.name}</Text>
            {data.image ? ( // check if any images exists
              <Image src={data.image} style={styles.photo} />  // render person's photo
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

const FamilyTreePDF = ({ onClose }) => {  // fetch family data from backend
const [familyData, setFamilyData] = useState({ nodes: [], edges: [] }); // state to hold family data, structured as nodes and edges
const [bookTitle, setBookTitle] = useState(''); // state for title of book
const [selectedPeople, setSelectedPeople] = useState([]); // state to track selected individuals
const [biographyLevel, setBiographyLevel] = useState('basic'); // state to manage level of detail for bio
const [isFormSubmitted, setIsFormSubmitted] = useState(false); // state to confirm if form has been submitted
const [availablePeople, setAvailablePeople] = useState([]); // state for storing available individuals to select from

useEffect(() => {
  // fetch family data from backend when component mounts
  const fetchData = async () => {
    try {
      // make a get request to retrieve family graph data
      const response = await axios.get('/api/family-graph-json');
      // update state with fetched family data
      setFamilyData(response.data);
      // extract and set available people from data
      setAvailablePeople(response.data.nodes.map(node => node.id));
    } catch (error) {
      // log any errors that occur during data fetching
      console.error('Error fetching data:', error);
    }
  };
  fetchData(); // call the function to execute fetch
}, []); // dependency array runs once on mount

const handleSubmit = (e) => {
  e.preventDefault(); // prevents default form submission behaviour
  setIsFormSubmitted(true); // sets state to indicate form has been submitted
};

const handlePersonSelection = (personId) => {
  setSelectedPeople(prev => 
    prev.includes(personId) // checks if person is already selected
      ? prev.filter(id => id !== personId) // if selected remove from list
      : [...prev, personId] // if not selected add them to list
  );
};

const handleSelectAll = (e) => {
  if (e.target.checked) { // checks if 'select all' checkbox is ticked
    setSelectedPeople(availablePeople); // selects available people
  } else {
    setSelectedPeople([]); // deselect all if checkbox is unticked
  }
};

const handleBiographyLevelChange = (e) => {
  setBiographyLevel(e.target.value); // updates the bio level based on user input
};

const renderPages = (graph) => {
  return [
    <FamilyTreeDiagram key="family-tree" selectedPeople={selectedPeople} graph={graph} />, // renders family tree diagram with selected people
    ...selectedPeople.map(id => {
      const person = graph.nodes.find(node => node.id === id); // finds person in graph by id
      return person ? (
        <PersonPage 
          key={`${person.id}-${biographyLevel}`} 
          person={person} 
          graph={graph} 
          biographyLevel={biographyLevel} 
          selectedPeople={selectedPeople}
        />
      ) : null; // returns null if person not found
    }).filter(Boolean) // filters out any null values from map
  ];
};

const overlay = {  // position when viewing PDF
  position: 'fixed', // fixed position stays when scrolling
  top: 0, // aligns overlay on top of viewpoint
  left: 0, // aligns overlay left of viewpoint
  width: '100%', // full width viewpoint
  height: '100%', // full height viewpoint
  backgroundColor: 'rgba(0, 0, 0, 0.5)', // semi transparent black background 
  display: 'flex', // flexbox for centring content
  justifyContent: 'center', // centres content horizontally 
  alignItems: 'center', // centres content vertically 
  zIndex: 1005, // high z-index ensures it appears above other elements
};

const closeButton = {  // button to close PDF view
  position: 'absolute', // positioning to place within overlay
  top: '10px', // distance from top of overlay
  left: '270px', // distance left of overlay
  padding: '5px 10px', // padding for button, better click area
  backgroundColor: '#00796b', // button background colour
  color: '#EDECD7', // button text colour 
  border: 'none', // no border cleaner look
  borderRadius: '5px', // rounded corners for button
  cursor: 'pointer', // changes cursor to pointer on hover
  fontSize: '1em', // standard font size readable
  fontFamily: '"Inika", serif', // serif stylish appearance 
  fontWeight: 'bold', // bold for emphasis 
};

const formStyle = {
  backgroundColor: 'white', // white background form
  padding: '20px', // padding around form for spacing
  borderRadius: '10px', // rounded corners, softer look
  display: 'flex', // flexbox arrange child elements
  flexDirection: 'column', // stacks children vertically 
  alignItems: 'center', // centres child elements horizontally 
};

const inputStyle = {
  margin: '10px 0', // vertical margin spacing between inputs
  padding: '5px', // padding inside input for comfort
  width: '300px', // fixed width uniformity 
  fontFamily: '"Inika", serif', // serif font for input text
};

const buttonStyle = {
  margin: '10px 0', // set margin above and below button
  padding: '5px 10px', // add padding inside
  backgroundColor: '#00796b', // define background colour
  color: '#EDECD7', // set button text colour 
  border: 'none', // remove border
  borderRadius: '5px', // add rounded corners
  cursor: 'pointer', // changes cursor to pointer on hover 
  fontSize: '1em', // font size for button text
  fontFamily: '"Inika", serif', // use inika font
  fontWeight: 'bold', // make button text bold
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
            type="text" // input field for book title
            value={bookTitle} // control input using bookTitle state
            onChange={(e) => setBookTitle(e.target.value)} // update state on input change
            placeholder="Enter the title for your family book" // placeholder text for input
            style={inputStyle} // apply custom styles to input
            required // make this field mandatory 
          />
          <Tippy content="Choose the level of detail for each person's biography">
            <select
              value={biographyLevel} // controlled select using bio level state
              onChange={handleBiographyLevelChange} // update state on selection change
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
                  id="select-all" // id checkbox to select/deselect all
                  checked={selectedPeople.length === availablePeople.length} // checks if all available people selected
                  onChange={handleSelectAll} // event handler changing selection
                />
              </Tippy>
              <label htmlFor="select-all">Select All</label>
            </div>
            {availablePeople.map(id => {  // iterate over each available person
               const node = familyData.nodes.find(node => node.id === id); // find corresponding node family data
               return (
                 <div key={id}>
                   <Tippy content={`Include or exclude ${node.data.name} from the book`}>
                     <input
                       type="checkbox" // input type checkbox for selection
                       id={`person-${id}`} // unique id for checkbox based on person id
                       checked={selectedPeople.includes(id)} // determine if checkbox should be checked
                       onChange={() => handlePersonSelection(id)} // handle change event for selection
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

export default FamilyTreePDF; // export family tree pdf component for use in other parts of application