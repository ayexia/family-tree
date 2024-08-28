import React, { useState, useEffect } from 'react'; //react modules handling states and effects of components
import Tree from 'react-d3-tree'; //Uses react-d3-tree package for visual representation of tree structure
import axios from 'axios'; //used to fetch api data through making http requests
import Tippy from '@tippyjs/react'; //uses tippyjs package to customise tooltip
import { Cake } from 'lucide-react';
import ReactCountryFlag from 'react-country-flag';
import 'tippy.js/dist/tippy.css';
import "../../css/treeCustomisation.css";
import Sidebar from './Sidebar';
import Legend from './Legend';

const FamilyTree = ({ generations, query, lineStyles }) => {
  const [treeData, setTreeData] = useState(null); //initialises variable treeData to store fetched family tree data
  const [isSidebarOpened, setIsSidebarOpened] = useState(false); //initialises, checks and sets visibility of sidebar (boolean)
  const [selectedNode, setSelectedNode] = useState(null); //initialises node selection state
  const [surnameQuery, setSurnameQuery] = useState('');
  const [hasSearched, setHasSearched] = useState(false); //initialises and checks if search has been performed (boolean)
  const [errorMessage, setErrorMessage] = useState(''); //initialises errorMessage variable to store error messages
  const [hoveredNode, setHoveredNode] = useState(null);
  const [images, setImages] = useState({});
  const [highlightQuery, setHighlightQuery] = useState('');

  const cityToCountryCode = {
    'New York': 'US',
    'Stratford-upon-Avon': 'GB',
    'Shottery, Warwickshire': 'GB',
    'Paris': 'FR',
  };

  useEffect(() => {
    if (hasSearched) {
      fetchFamilyTreeData(surnameQuery); //after component is mounted calls this function which retrieves the family tree data from the api through http requests
    }
  }, [surnameQuery, generations, hasSearched]);

  useEffect(() => {
    if (query) {
      setHighlightQuery(query.toLowerCase());
    } else {
      setHighlightQuery ('');
    }
  }, [query]);

  const fetchFamilyTreeData = async (surname = '') => { 
    try {
      const response = await axios.get('/api/family-tree-json', {
        params: { desiredSurname: surname, generations }
      }); //uses axios library to make http request to fetch api data, which is then parsed as JSON. this retrieves data for a queried surname in particular
      if (response.data.length === 0) {
        setErrorMessage('No results found for the given surname.'); //if no results found prints error message
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

  const isBirthday = (dob) => {
      if (!dob) return false;
      const today = new Date();
      const birthDate = new Date(dob);
      return today.getMonth() === birthDate.getMonth() && today.getDate() === birthDate.getDate();
    };

  const customNode = ({ nodeDatum }) => { //customises nodes in family tree based on particular properties
    const selectedImage = images[nodeDatum.id] || nodeDatum.attributes.image || '/images/user.png'; //node's image is either the image selected by user or default image
    const isMale = nodeDatum.attributes.gender === 'M'; //checks if node's gender is male or female (for spouses, only main person's gender is counted)
    const isFemale = nodeDatum.attributes.gender === 'F';
    const isHighlighted = highlightQuery && nodeDatum.name.toLowerCase().includes(highlightQuery);
    const isTodayBirthday = isBirthday(nodeDatum.attributes.DOB);
    const countryCode = nodeDatum.attributes.birth_place ? cityToCountryCode[nodeDatum.attributes.birth_place] : null;

    const shouldHighlight = isHighlighted || isTodayBirthday;
    const nodeStyle = {
      stroke: shouldHighlight ? (isTodayBirthday ? '#FFD700' : 'yellow') : (isMale ? '#97EBE6' : isFemale ? '#EB97CF' : '#EBC097'),
      fill: 'none',
      strokeWidth: shouldHighlight ? 15 : 10,
    };

    const spouses = nodeDatum.spouses || [];
    const spouseSpacing = 400; 
    const verticalSpacing = 55;
    const nodeRadius = 50;
    const line = nodeRadius + 10;

  const toolTip = (node) => (//customises tooltip, containing names and marriage info (if applicable)
      <div style={{ 
        padding: '10px', 
        background: 'linear-gradient(135deg, #92B08E, #6C9661, #37672F)',
        color: '#fff', 
        borderRadius: '10px' 
      }}>
      <strong style={{ fontSize: '20px', fontFamily: 'Times New Roman' }}>{node.name}</strong><br />
      {node.attributes.marriage_dates && node.attributes.marriage_dates.length > 0 ? (
          node.attributes.marriage_dates.map((marriage, index) => (
            <div key={index}>
              <p>Marriage {index + 1}: {marriage || 'Unknown date'}</p>
              {node.attributes.divorce_dates && node.attributes.divorce_dates[index] && (
                <p>Divorce {index + 1}: {node.attributes.divorce_dates[index]}</p>
              )}
            </div>
          ))
        ) : (
          <p>No marriages</p>
        )}
        {node.attributes.parents && Object.keys(node.attributes.parents).length > 0 ? (
            <ul>
              {Object.values(node.attributes.parents).map(parent => {
                let parentType = parent.gender === 'F' ? 'Mother' : parent.gender === 'M' ? 'Father' : 'Parent';
                return (
                  <li key={parent.id}>
                    {parentType}: {parent.name || 'Unknown person'}
                  </li>
                );
              })}
            </ul>
          ) : (
            <ul>
            <li>Mother: Unknown person</li>
            <li>Father: Unknown person</li>
            </ul>
          )}
      </div>
    );

  const nodeHover = (node, isSpouse = false) => {
    setHoveredNode({ node, isSpouse });
  };

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
                <circle r={nodeRadius} style={nodeStyle} />
                <image
                  href={selectedImage}
                  x="-50"
                  y="-50"
                  width="100"
                  height="100"
                  clipPath="url(#clipCircle)"
                />
                {isTodayBirthday && (
                <Cake
                  size={24}
                  color="#FFD700"
                  style={{
                    transform: 'translate(30px, -60px)',
                  }}
                />
                )}
                {countryCode && (
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
                <text fill="#37672F" stroke="none" x="60" y="-5" style={{ fontSize: '24px', fontFamily: 'Times New Roman' }}>
                  {nodeDatum.name}
                </text>
                <text fill="#37672F" stroke="none" x="60" y="15" style={{ fontSize: '20px' }}>
                  DOB: {nodeDatum.attributes.DOB}
                </text>
                <text fill="#37672F" stroke="none" x="60" y="35" style={{ fontSize: '20px' }}>
                  DOD: {nodeDatum.attributes.DOD}
                </text>
              </g>
              {spouses.length > 0 && (
                <g>
                  {spouses.map((spouse, index) => {
                  const isFirstRow = index < 2;
                  const isLeft = index % 2 === 0;
                  const row = isFirstRow ? 0 : Math.floor((index - 2) / 2) + 1;
                  const horizontalPosition = isFirstRow ? (isLeft ? -spouseSpacing : spouseSpacing) : (isLeft ? -spouseSpacing : spouseSpacing);
                  const verticalPosition = isFirstRow ? 0 : row * verticalSpacing;
                  const isSpouseHighlighted = highlightQuery && spouse.name.toLowerCase().includes(highlightQuery);
                  const spouseLineStyle = spouse.is_current ? lineStyles.current : 
                  (spouse.attributes.divorce_dates && spouse.attributes.divorce_dates.length > 0) ? lineStyles.divorced : 
                  lineStyles.current;                  
                  const isSpouseBirthday = isBirthday(spouse.attributes.DOB);
                  const spouseCountryCode = spouse.attributes.birth_place ? cityToCountryCode[spouse.attributes.birth_place] : null;

                  return (
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
                        x1={isFirstRow ? (isLeft ? (spouseSpacing - line) : (-spouseSpacing + line)) : 0}
                        y1={0}
                        x2={isFirstRow ? (isLeft ? (line) : (-line)) : 0}
                        y2={-verticalPosition}
                        stroke={spouseLineStyle.color}
                        strokeWidth={spouseLineStyle.width}
                        strokeDasharray={spouseLineStyle.dashArray}
                      />
                        <circle r={nodeRadius} style={{
                          stroke: isSpouseHighlighted || isSpouseBirthday ? (isSpouseBirthday ? '#FFD700' : 'yellow') : spouse.attributes.gender === 'M' ? '#97EBE6' : spouse.attributes.gender === 'F' ? '#EB97CF' : '#EBC097',
                          fill: 'none',
                          strokeWidth: isSpouseHighlighted || isSpouseBirthday ? 15 : 10,
                        }} />
                        <image
                          href={images[spouse.id] || spouse.attributes.image || '/images/user.png'}
                          x="-50"
                          y="-50"
                          width="100"
                          height="100"
                          clipPath="url(#clipCircle)"
                        ></image>
                        {isSpouseBirthday && (
                        <Cake
                          size={24}
                          color="#FFD700"
                          style={{
                            transform: 'translate(30px, -60px)',
                          }}
                        />
                      )}
                      {spouseCountryCode && (
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
                        <text fill="#37672F" stroke="none" x="60" y="-5" style={{ fontSize: '24px', fontFamily: 'Times New Roman' }}>
                          {spouse.name}
                        </text>
                        <text fill="#37672F" stroke="none" x="60" y="15" style={{ fontSize: '20px' }}>
                          DOB: {spouse.attributes.DOB}
                        </text>
                        <text fill="#37672F" stroke="none" x="60" y="35" style={{ fontSize: '20px' }}>
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

    const pathClassFunc = () => {
      return 'parent-child-link';
    };
  
    const linkStyles = `
      .parent-child-link {
        stroke: ${lineStyles.parentChild.color} !important;
        stroke-width: ${lineStyles.parentChild.width}px !important;
        stroke-dasharray: ${lineStyles.parentChild.dashArray} !important;
      }
    `;

    const searchContainerStyle = {
      display: 'flex',
      alignItems: 'center',
      margin: '12px',
      width: '27.5%',
    };
  
    const inputStyle = {
      flex: 1,
      padding: '2.5px',
      fontSize: '0.85em',
      fontFamily: '"Inika", serif',
      border: '1px solid #CCE7BD',
      borderRadius: '5px 0 0 5px',
      outline: 'none',
    };
  
    const buttonStyle = {
      padding: '2.5px 20px',
      backgroundColor: '#CCE7BD',
      color: '#587353',
      border: 'none',
      borderRadius: '0 5px 5px 0',
      cursor: 'pointer',
      fontSize: '0.85em',
      fontFamily: '"Inika", serif',
      fontWeight: 'bold',
      transition: 'background-color 0.3s',
    };

    const imgStyle = {
      width: '15px',
      height: '15px',
      opacity: 0.3,
  };

    if (!treeData) { //alternate display if no tree data is available - error message and search bar
  return (
    <div>
      {errorMessage}
      <div style={searchContainerStyle}>
        <input 
          type="text" 
          value={surnameQuery} 
          onChange={(e) => setSurnameQuery(e.target.value)} 
          placeholder="Search a bloodline (surname)"
          style={inputStyle}
        />
        <button onClick={searchSurname} style={buttonStyle}>
        <img src="/images/search.png" alt="Search" style={imgStyle}></img></button>
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
      <img src="/images/search.png" alt="Search" style={imgStyle}></img></button>
  </div>
  <div style={{ flex: '1', height: '100%', width: '100%' }}>
    {hasSearched && treeData && !errorMessage && (
      <Tree
        data={treeData}
        orientation="vertical"
        pathFunc="step"        
        pathClassFunc={pathClassFunc}
        translate={{ x: 300, y: 50 }}
        separation={{ siblings: 4, nonSiblings: 5 }}
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