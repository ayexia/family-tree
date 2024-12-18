import React, { useState, useEffect } from 'react'; //react modules handling states and effects of components
import Tree from 'react-d3-tree'; //Uses react-d3-tree package for visual representation of tree structure
import axios from 'axios'; //used to fetch api data through making http requests
import Tippy from '@tippyjs/react'; //uses tippyjs package to customise tooltip - https://github.com/atomiks/tippyjs
//displays cake icon for birthday celebration - https://lucide.dev/icons/cake 
import { Cake } from 'lucide-react';
//displays country flags based on birth location - https://github.com/lazicnemanja/react-country-flags
import ReactCountryFlag from 'react-country-flag';
//imports necessary tooltip styling
import 'tippy.js/dist/tippy.css';
//imports component for showing detailed member information
import Sidebar from './Sidebar';
//imports component for displaying line style information
import Legend from './Legend';

const FamilyTree = ({ generations, lineStyles, desiredName }) => {
  const [treeData, setTreeData] = useState(null); //initialises variable treeData to store fetched family tree data
  const [isSidebarOpened, setIsSidebarOpened] = useState(false); //initialises, checks and sets visibility of sidebar (boolean)
  const [selectedNode, setSelectedNode] = useState(null); //initialises node selection state
  //stores current surname being searched for
  const [surnameQuery, setSurnameQuery] = useState('');
  const [hasSearched, setHasSearched] = useState(false); //initialises and checks if search has been performed (boolean)
  const [errorMessage, setErrorMessage] = useState(''); //initialises errorMessage variable to store error messages
  //tracks which node user is currently hovering over
  const [hoveredNode, setHoveredNode] = useState(null);
  //stores all uploaded member images
  const [images, setImages] = useState({});
  //keeps track of currently highlighted search result
  const [highlightedPerson, setHighlightedPerson] = useState(null);

  //maps city names to their corresponding country codes for flag display
  const cityToCountryCode = {
    'New York': 'US',
    'Stratford-upon-Avon': 'GB',
    'Shottery, Warwickshire': 'GB',
    'Aston': 'GB',
    'Paris': 'FR',
  };

  useEffect(() => {
    if (hasSearched) {
      fetchFamilyTreeData(surnameQuery); //after component is mounted calls this function which retrieves the family tree data from the api through http requests
    }
 }, [surnameQuery, generations, hasSearched]);
 
 //watches for changes in search name and updates highlighting
 useEffect(() => {
    if (desiredName) {
      setHighlightedPerson(desiredName.toLowerCase());
    } else {
      setHighlightedPerson(null);
    }
 }, [desiredName]);
 
 const fetchFamilyTreeData = async (surname = '') => { 
    try {
      const response = await axios.get('/api/family-tree-json', {
        params: { desiredSurname: surname, generations }
      }); //uses axios library to make http request to fetch api data, which is then parsed as JSON. this retrieves data for a queried surname
      if (response.data.length === 0) {
        setErrorMessage('No results found for the given search.'); //if no results found prints error message
        setTreeData(null);
      } else {
        setTreeData(response.data); //the fetched data is stored in treeData variable
        setErrorMessage(''); //clears error message      
      }
    } catch (error) {
      setErrorMessage('An error occurred while fetching data.'); //error message if any issues with fetching data
    }
 };
 
 const searchSurname = () => { //search function
    setErrorMessage('');
    setHasSearched(true); //sets hasSearched to true, thus allowing the tree data to be shown (or an error message if nothing is found)
    fetchFamilyTreeData(surnameQuery); //calls function to retrieve the family tree data for queried surname
 };
 
 const openSidebar = (node) => { //if user clicks a node, sets that as selectedNode and sets sideBar opened to true, opening it and displaying information for that node
    setSelectedNode(node);
    setIsSidebarOpened(true); 
 };
 
 const closeSidebar = () => { //if user closes sidebar, closes the sidebar and deselects the node (thus setting selectedNode to null)
    setIsSidebarOpened(false);
    setSelectedNode(null); 
 };
 
 //checks if today matches a member's birth date
 const isBirthday = (birthDate) => {
    //returns false if no birth date exists
    if (!birthDate) return false;
    //gets current date
    const today = new Date();
    //splits birth date into components
    const [year, month, day] = birthDate.split('-').map(Number);
    
    //creates date object for this year's birthday
    const birthdayThisYear = new Date(today.getFullYear(), month - 1, day);
    
    //checks if today matches birthday month and day
    return today.getMonth() === birthdayThisYear.getMonth() && today.getDate() === birthdayThisYear.getDate();
 };

 const customNode = ({ nodeDatum }) => { //customises nodes in family tree based on particular properties
  const selectedImage = images[nodeDatum.id] || nodeDatum.attributes.image || '/images/user.png'; //node's image is either the image selected by user or default image
  const isMale = nodeDatum.attributes.gender === 'M'; //checks if node's gender is male or female (for spouses, only main person's gender is counted)
  const isFemale = nodeDatum.attributes.gender === 'F';
  //checks if this node matches current search term
  const isHighlighted = highlightedPerson && (
    nodeDatum.name.toLowerCase().includes(highlightedPerson) ||
    (nodeDatum.attributes.surname && nodeDatum.attributes.surname.toLowerCase().includes(highlightedPerson))
  );
  //checks if today is this member's birthday
  const isTodayBirthday = isBirthday(nodeDatum.attributes.DOB);
  //gets country code for member's birth place if it exists
  const countryCode = nodeDatum.attributes.birth_place ? cityToCountryCode[nodeDatum.attributes.birth_place] : null;
  //checks if member is adopted
  const isAdopted = nodeDatum.attributes.isAdopted;
  //determines node shape based on adoption status
  const nodeShape = isAdopted ? 'polygon' : 'circle';

  //determines if node should be highlighted for birthday or search
  const shouldHighlight = isHighlighted || isTodayBirthday;
  //sets node styling based on gender and highlight status
  const nodeStyle = {
    stroke: shouldHighlight ? (isTodayBirthday ? '#FFD700' : 'yellow') : 
      (isMale ? lineStyles.nodeMale.color : 
       isFemale ? lineStyles.nodeFemale.color : 
       lineStyles.nodeOther.color),
    fill: 'none',
    strokeWidth: shouldHighlight ? 15 : 10,
  };

  //gets array of spouse nodes if any exist
  const spouses = nodeDatum.spouses || [];
  //defines spacing between main node and spouse nodes
  const spouseSpacing = 400; 
  //defines vertical spacing for multiple spouses
  const verticalSpacing = 55;
  //defines size of node circle
  const nodeRadius = 50;
  //defines length of connecting lines
  const line = nodeRadius + 10;

  const toolTip = (node) => { //customises tooltip, containing names and marriage info (if applicable)
    const formatDate = (date) => {
        //returns date if it exists and is valid, otherwise returns unknown
        return date && date !== 'Unknown date' ? date : 'Unknown date';
    };
 
    const getTimelineEvents = (person) => {
        //initialises timeline with birth event
        let events = [
            { date: formatDate(person.attributes.DOB), event: 'Born', sortDate: person.attributes.DOB || '0000-00-00', order: 0 }
        ];
 
        //adds marriage events to timeline if they exist
        (person.attributes.marriage_dates || []).forEach((date, index) => {
            events.push({
                date: formatDate(date),
                event: `Married (${index + 1})`,
                sortDate: date || '9998-99-99',
                order: date ? 1 : 2
            });
        });
 
        //adds divorce events to timeline if they exist
        (person.attributes.divorce_dates || []).forEach((date, index) => {
            if (date && date !== 'Unknown date') {
                events.push({
                    date: formatDate(date),
                    event: `Divorced (${index + 1})`,
                    sortDate: date,
                    order: 1
                });
            }
        });
 
        //adds death event to timeline if it exists
        if (person.attributes.DOD) {
            events.push({
                date: formatDate(person.attributes.DOD),
                event: 'Died',
                sortDate: person.attributes.DOD,
                order: 3
            });
        }
 
        //sorts events by order then by date
        return events.sort((a, b) => {
            if (a.order !== b.order) {
                return a.order - b.order;
            }
            return a.sortDate.localeCompare(b.sortDate);
        });
    };
 
    //gets all timeline events for this person
    const timelineEvents = getTimelineEvents(node);
 
    return (
        <div style={{ 
            padding: '10px', 
            background: 'linear-gradient(135deg, #00796b, #6C9661, #37672F)',
            color: '#edecd7', 
            borderRadius: '10px',
            width: '300px'
        }}>
            <strong style={{ fontSize: '20px', fontFamily: 'Inika' }}>{node.name}</strong><br />
            {timelineEvents.length > 0 && (
                <div style={{ marginTop: '10px', borderLeft: '2px solid #edecd7', paddingLeft: '10px' }}>
                    {timelineEvents.map((event, index) => (
                        <div key={index} style={{ marginBottom: '5px', position: 'relative' }}>
                            <div style={{ 
                                width: '10px', 
                                height: '10px', 
                                borderRadius: '50%', 
                                background: '#edecd7', 
                                position: 'absolute', 
                                left: '-16px', 
                                top: '5px' 
                            }}></div>
                            <strong>{event.date}</strong>: {event.event}
                        </div>
                    ))}
                </div>
            )}
            <div style={{ marginTop: '10px' }}>
                <strong>Parents:</strong>
                {node.attributes.parents && Object.keys(node.attributes.parents).length > 0 ? (
                    <ul style={{ paddingLeft: '20px', marginTop: '5px' }}>
                        {Object.values(node.attributes.parents).map(parent => {
                            //determines parent type based on gender
                            let parentType = parent.gender === 'F' ? 'Mother' : parent.gender === 'M' ? 'Father' : 'Parent';
                            return (
                                <li key={parent.id}>
                                    {parentType}: {parent.name || 'Unknown person'}
                                </li>
                            );
                        })}
                    </ul>
                ) : (
                    <ul style={{ paddingLeft: '20px', marginTop: '5px' }}>
                        <li>Mother: Unknown person</li>
                        <li>Father: Unknown person</li>
                    </ul>
                )}
            </div>
        </div>
    );
 };
 
 //updates hoveredNode state when mouse enters node
 const nodeHover = (node, isSpouse = false) => {
    setHoveredNode({ node, isSpouse });
 };
 
 //returns tooltip content based on hovered node
 const tooltipContent = () => {
    if (hoveredNode) {
        return hoveredNode.isSpouse ? toolTip(hoveredNode.node) : toolTip(nodeDatum);
    }
    return null;
 };

 return (
  <>
      <Tippy content={tooltipContent()} arrow={false}>
          <g>
              <g onClick={() => openSidebar(nodeDatum)}
                  onMouseEnter={() => nodeHover(nodeDatum)}
                  onMouseLeave={() => setHoveredNode(null)}>
                  {nodeShape === 'circle' ? (
                      //renders circular node for non-adopted members
                      <circle r={nodeRadius} style={nodeStyle} />
                  ) : (
                      //renders hexagonal node for adopted members
                      <polygon 
                          points={`0,-${nodeRadius} ${nodeRadius},-${nodeRadius/2} ${nodeRadius},${nodeRadius/2} 0,${nodeRadius} -${nodeRadius},${nodeRadius/2} -${nodeRadius},-${nodeRadius/2}`} 
                          style={nodeStyle} 
                      />
                  )}
                  <image
                      href={selectedImage}
                      x="-50"
                      y="-50"
                      width="100"
                      height="100"
                      clipPath="url(#clipCircle)"
                  />
                  {isTodayBirthday && (
                      //shows cake icon if it's member's birthday
                      <Cake
                          size={24}
                          color="#FFD700"
                          style={{
                              transform: 'translate(30px, -60px)',
                          }}
                      />
                  )}
                  {countryCode && (
                      //shows country flag based on birth place
                      <foreignObject x="-60" y="-60" width="24" height="24">
                          <ReactCountryFlag 
                              countryCode={countryCode}
                              svg 
                              style={{ width: '1.5em', height: '1.5em' }}
                          />
                      </foreignObject>
                  )}
                  <defs>
                      <clipPath id="clipCircle">
                          <circle cx="0" cy="0" r={nodeRadius} />
                      </clipPath>
                  </defs>
                  <text fill="#00796b" stroke="none" x="60" y="-5" style={{ fontSize: '28px', fontFamily: 'Times New Roman' }}>
                      {nodeDatum.name}
                  </text>
                  <text fill="#00796b" stroke="none" x="60" y="15" style={{ fontSize: '24px' }}>
                      DOB: {nodeDatum.attributes.DOB}
                  </text>
                  <text fill="#00796b" stroke="none" x="60" y="35" style={{ fontSize: '24px' }}>
                      DOD: {nodeDatum.attributes.DOD}
                  </text>
              </g>
              {spouses.length > 0 && (
                  //container for spouse nodes
                  <g>
                      {spouses.map((spouse, index) => {
                          // Position Calculation:
                            const isFirstRow = index < 2;  // First two spouses go in first row
                            const isLeft = index % 2 === 0;  // Alternates between left and right
                            const row = isFirstRow ? 0 : Math.floor((index - 2) / 2) + 1;  // Calculates which row for 3+ spouses

                            // Example:
                            // Spouse 0: First row, left  (-spaceSpacing, 0)
                            // Spouse 1: First row, right (+spaceSpacing, 0)
                            // Spouse 3: Second row, left (-spaceSpacing, verticalSpacing)
                            // Spouse 4: Second row, right (+spaceSpacing, verticalSpacing)

                            // Position Calculation:
                            const horizontalPosition = isFirstRow 
                                ? (isLeft ? -spouseSpacing : spouseSpacing)  // First row: alternate left/right
                                : (isLeft ? -spouseSpacing : spaceSpacing);  // Other rows: same pattern

                            const verticalPosition = isFirstRow 
                                ? 0  // First row: no vertical offset
                                : row * verticalSpacing;  // Other rows: offset based on row number

                          //checks if spouse matches search term
                          const isSpouseHighlighted = highlightedPerson && spouse.name.toLowerCase().includes(highlightedPerson);
                          //determines line style based on marriage status
                          const spouseLineStyle = spouse.is_current ? lineStyles.current : 
                          (spouse.attributes.divorce_dates && spouse.attributes.divorce_dates.length > 0) ? lineStyles.divorced : 
                          lineStyles.current;                  
                          //checks if today is spouse's birthday
                          const isSpouseBirthday = isBirthday(spouse.attributes.DOB);
                          //gets country code for spouse's birth place
                          const spouseCountryCode = spouse.attributes.birth_place ? cityToCountryCode[spouse.attributes.birth_place] : null;
                          //checks if spouse is adopted
                          const isSpouseAdopted = spouse.attributes.isAdopted;
                          //determines node shape based on adoption status
                          const spouseNodeShape = isSpouseAdopted ? 'polygon' : 'circle';
            
                          return (
                            //creates container for each spouse node with positioning and event handlers
                            <g key={spouse.id}
                                transform={`translate(${horizontalPosition}, ${verticalPosition})`}
                                onMouseEnter={(e) => {
                                    e.stopPropagation();
                                    nodeHover(spouse, true);
                                }}
                                onMouseLeave={() => setHoveredNode(null)}
                                onClick={(e) => {
                                    e.stopPropagation();
                                    openSidebar(spouse);
                                }}
                            >
                                <line
                                    // Starting point of line
                                    x1={isFirstRow ? (isLeft ? (spouseSpacing - line) : (-spouseSpacing + line)) : 0}
                                    y1={0}
                                    // Ending point of line
                                    x2={isFirstRow ? (isLeft ? (line) : (-line)) : 0}
                                    y2={-verticalPosition}
                                    // Line styling based on relationship
                                    stroke={spouseLineStyle.color}  // Red for current, Grey for divorced
                                    strokeWidth={spouseLineStyle.width}
                                    strokestrokeDasharray={spouseLineStyle.dashArray}  // Solid or dashed
                                />
                                {spouseNodeShape === 'circle' ? (
                                    //renders circular node for non-adopted spouses
                                    <circle r={nodeRadius} style={{
                                        stroke: isSpouseHighlighted || isSpouseBirthday ? (isSpouseBirthday ? '#FFD700' : 'yellow') : 
                                            spouse.attributes.gender === 'M' ? lineStyles.nodeMale.color : 
                                            spouse.attributes.gender === 'F' ? lineStyles.nodeFemale.color : 
                                            lineStyles.nodeOther.color,
                                        fill: 'none',
                                        strokeWidth: isSpouseHighlighted || isSpouseBirthday ? 15 : 10,
                                    }} />
                                ) : (
                                    //renders hexagonal node for adopted spouses
                                    <polygon 
                                        points={`0,-${nodeRadius} ${nodeRadius},-${nodeRadius/2} ${nodeRadius},${nodeRadius/2} 0,${nodeRadius} -${nodeRadius},${nodeRadius/2} -${nodeRadius},-${nodeRadius/2}`} 
                                        style={{
                                            stroke: isSpouseHighlighted || isSpouseBirthday ? (isSpouseBirthday ? '#FFD700' : 'yellow') : 
                                                spouse.attributes.gender === 'M' ? lineStyles.nodeMale.color : 
                                                spouse.attributes.gender === 'F' ? lineStyles.nodeFemale.color : 
                                                lineStyles.nodeOther.color,
                                            fill: 'none',
                                            strokeWidth: isSpouseHighlighted || isSpouseBirthday ? 15 : 10,
                                        }} 
                                    />
                                )}
                                <image
                                    href={images[spouse.id] || spouse.attributes.image || '/images/user.png'}
                                    x="-50"
                                    y="-50"
                                    width="100"
                                    height="100"
                                    clipPath="url(#clipCircle)"
                                ></image>
                                {isSpouseBirthday && (
                                    //shows cake icon if it's spouse's birthday
                                    <Cake
                                        size={24}
                                        color="#FFD700"
                                        style={{
                                            transform: 'translate(30px, -60px)',
                                        }}
                                    />
                                )}
                                {spouseCountryCode && (
                                    //shows country flag based on spouse's birth place
                                    <foreignObject x="-60" y="-60" width="24" height="24">
                                        <ReactCountryFlag 
                                            countryCode={spouseCountryCode}
                                            svg 
                                            style={{ width: '1.5em', height: '1.5em' }}
                                        />
                                    </foreignObject>
                                )}
                                <defs>
                                    <clipPath id="clipCircle">
                                        <circle cx="0" cy="0" r={nodeRadius} />
                                    </clipPath>
                                </defs>
                                <text fill="#00796b" stroke="none" x="60" y="-5" style={{ fontSize: '28px', fontFamily: 'Times New Roman' }}>
                                    {spouse.name}
                                </text>
                                <text fill="#00796b" stroke="none" x="60" y="15" style={{ fontSize: '24px' }}>
                                    DOB: {spouse.attributes.DOB}
                                </text>
                                <text fill="#00796b" stroke="none" x="60" y="35" style={{ fontSize: '24px' }}>
                                    DOD: {spouse.attributes.DOD}
                                </text>
                            </g>
                        );
                    })}
                </g>
              )}
          </g>
      </Tippy>
  </>
  );
};
  
//defines style for parent-child relationship lines
const pathClassFunc = () => {
    return 'parent-child-link';
 };
 
 //sets styling for parent-child connection lines
 const linkStyles = `
    .parent-child-link {
        stroke: ${lineStyles.parentChild.color} !important;
        stroke-width: ${lineStyles.parentChild.width}px !important;
        stroke-dasharray: ${lineStyles.parentChild.dashArray} !important;
    }
 `;
 
 //styling for search container element
 const searchContainerStyle = {
    display: 'flex',
    alignItems: 'center',
    margin: '12px',
    width: '27.5%',
 };
 
 //styling for search input field
 const inputStyle = {
    flex: 1,
    padding: '2.5px',
    fontSize: '0.85em',
    fontFamily: '"Inika", serif',
    border: '1px solid #CCE7BD',
    borderRadius: '5px 0 0 5px',
    outline: 'none',
 };
 
 //styling for search button
 const buttonStyle = {
    padding: '2.5px 20px',
    backgroundColor: '#00796b',
    color: '#587353',
    border: 'none',
    borderRadius: '0 5px 5px 0',
    cursor: 'pointer',
    fontSize: '0.85em',
    fontFamily: '"Inika", serif',
    fontWeight: 'bold',
    transition: 'background-color 0.3s',
 };
 
 //styling for search icon
 const imgStyle = {
    width: '15px',
    height: '15px',
    opacity: 0.8,
 };
 
 if (!treeData) { //alternate display if no tree data is available - error message and search bar
    return (
        <div>
            {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
            <div style={searchContainerStyle}>
                <input 
                    type="text" 
                    value={surnameQuery} 
                    onChange={(e) => setSurnameQuery(e.target.value)} 
                    placeholder="Search a bloodline (surname)"
                    style={inputStyle}
                />
                <button onClick={searchSurname} style={buttonStyle}>
                    <img src="/images/search.png" alt="Search" style={imgStyle}></img>
                </button>
            </div>
        </div>
    );
 }

 return ( //utilises react-d3-tree library to set parameters for tree display
    //sets width and height of display, places search bar, the data to be used, any node customisations the orientation of the tree and style of links/branches, positioning of tree and spacing between sibling and non-sibling nodes
    //only appears if the user has searched a bloodline/surname where family tree data is available, and no errors were given
    //also ensures sidebar is only opened if isSidebarOpened is true, and if so will display the data of a selected node and also close if user selects to do this
    <div style={{ display: 'flex', flexDirection: 'column', width: '100%', height: '100vh' }}>
        <style>{linkStyles}</style>
        <div style={searchContainerStyle}>
            <input 
                type="text" 
                value={surnameQuery} 
                onChange={(e) => setSurnameQuery(e.target.value)} 
                placeholder="Search a bloodline (surname)"
                style={inputStyle}
            />
            <button onClick={searchSurname} style={buttonStyle}>
                <img src="/images/search.png" alt="Search" style={imgStyle}></img>
            </button>
        </div>
        <div style={{ flex: '1', height: '100%', width: '100%' }}>
            {(hasSearched || desiredName) && treeData && !errorMessage && (
                //renders tree component with configuration
                <Tree
                    data={treeData}
                    orientation="vertical"
                    pathFunc="step"        
                    pathClassFunc={pathClassFunc}
                    translate={{ x: 300, y: 50 }}
                    separation={{ siblings: 5.5, nonSiblings: 5 }}
                    nodeSize={{ x: 190, y: 300 }}
                    renderCustomNodeElement={customNode}
                />
            )}
        </div>
        {isSidebarOpened && <Sidebar node={selectedNode} onClose={closeSidebar} setImages={setImages} images={images} />}
        <Legend lineStyles={lineStyles} />
    </div>
 );
 };
 
 export default FamilyTree; //exports component for use