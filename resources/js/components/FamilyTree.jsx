import React, { useState, useEffect } from 'react'; //react modules handling states and effects of components
import Tree from 'react-d3-tree'; //Uses react-d3-tree package for visual representation of tree structure
import axios from 'axios'; //used to fetch api data through making http requests
import Tippy from '@tippyjs/react'; //uses tippyjs package to customise tooltip
import 'tippy.js/dist/tippy.css';
import "../../css/treeCustomisation.css";
import Sidebar from './Sidebar';

const FamilyTree = () => {
  const [treeData, setTreeData] = useState(null); //initialises variable treeData to store fetched family tree data
  const [setImages] = useState({}); //initialises setter method for storing image paths
  const [isSidebarOpened, setIsSidebarOpened] = useState( false ); //initialises, checks and sets visibility of sidebar (boolean)
  const [selectedNode, setSelectedNode] = useState(null); //initialises node selection state
  const [query, setQuery] = useState(''); //initialises search function to store query 
  const [hasSearched, setHasSearched] = useState(false); //initialises and checks if search has been performed (boolean)
  const [errorMessage, setErrorMessage] = useState(''); //initialises errorMessage variable to store error messages

  useEffect(() => {
    fetchFamilyTreeData(); //after component is mounted calls this function which retrieves the family tree data from the api through http requests
  }, []);

  const fetchFamilyTreeData = async (surname= '') => { 
    try {
      const response = await axios.get('/api/family-tree-json', {
        params: { desiredSurname: surname }
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
 
  const search = () => { //search function
    setErrorMessage('');
    setHasSearched(true); //sets hasSearched to true, thus allowing the tree data to be shown (or an error message if nothing is found)
    fetchFamilyTreeData(query); //calls function to retrieve the family tree data for queried surname
  };

  const uploadImage = async (event, node) => { //function for uploading images
    const selectedFile = event.target.files[0]; //sets the selectedFile as the first file the user selects
    if (!selectedFile) return; //if no file has been selected do nothing
  
    const formData = new FormData(); //new formdata object is created (used for build the data needed to pass to the server)
    formData.append('image', selectedFile); //adds the selected file as a value for key "image" for formdata
    formData.append('id', node); //adds the selected node's id as a value for key "id" for formdata (used to associate the image with the correct node)
  
    try { //sends request to upload-image endpoint with formdata as the data
      const response = await axios.post('/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data', //necessary for file uploads
        },
      });
      setImages({ [node]: response.data.imagePath }); //updates state with new image and sets it to node
    } catch (error) {
      setErrorMessage('Image could not be uploaded.'); //error message if image cannot be uploaded for any reason
    }
  };
  
  const openSidebar = (node) => { //if user clicks a node, sets that as selectedNode and sets sideBar opened to true, opening it and displaying information for that node
      setSelectedNode(node);
      setIsSidebarOpened(true); 
    };

  const closeSidebar = () => { //if user closes sidebar, closes the sidebar and deselects the node (thus setting selectedNode to null)
      setIsSidebarOpened(false);
      setSelectedNode(null); 
    };


  const customNode = ({ nodeDatum }) => { //customises nodes in family tree based on particular properties
    const selectedImage = nodeDatum.attributes.image || '/images/user.png'; //node's image is either the image selected by user or default image
    const isMale = nodeDatum.attributes.gender === 'M'; //checks if node's gender is male or female (for spouses, only main person's gender is counted)
    const isFemale = nodeDatum.attributes.gender === 'F';
    const nodeStyle = {
      stroke: isMale ? '#97EBE6' : isFemale ? '#EB97CF': '#EBC097', //changes outline colour of node depending on gender (alternative colour if neither or unknown)
      fill: 'none',
      strokeWidth: 10,
    };

    const spouses = nodeDatum.spouses || [];
    const spouseSpacing = 400; 
    const verticalSpacing = 55;

  const toolTip = ( //customises tooltip, containing names and marriage info (if applicable)
      <div style={{ 
        padding: '10px', 
        background: 'linear-gradient(135deg, #92B08E, #6C9661, #37672F)',
        color: '#fff', 
        borderRadius: '10px' 
      }}>
      <strong style={{ fontSize: '20px', fontFamily: 'Times New Roman' }}>{nodeDatum.name}</strong><br />
        {nodeDatum.attributes.marriage}<br />
        {nodeDatum.attributes.divorce}
    </div>
  );

  const handleSpouseClick = (event, spouse) => {
    event.stopPropagation();
    openSidebar(spouse);
  };

//returns custom node features: tooltip with desired information and appearance, 
      //onClick function which calls openSidebar on a node to display its details, provided the user clicks any of the properties of that specific node,
      //styles the node to contain an image in a circular fashion, with the image filling its contents (any extra is clipped off),
      //contains the names, DOBs and DODs for the people of that specific node and a button for users to upload images to their node of choice
    return (<> 
<Tippy content={toolTip}>
  <g onClick={() => openSidebar(nodeDatum)}>
    <circle r={50} style={nodeStyle} />
    <image
      href={selectedImage}
      x="-50"
      y="-50"
      width="100"
      height="100"
      clipPath="url(#clipCircle)"
    />
    <defs>
      <clipPath id="clipCircle">
        <circle cx="0" cy="0" r="50" />
      </clipPath>
    </defs>
    <text fill="#37672F" stroke="none" x="60" y="-5" style={{ fontSize: '24px', fontFamily: 'Times New Roman' }}>
      {nodeDatum.name}
    </text>
    <text fill="#37672F" stroke="none" x="60" y="15" style={{ fontSize: '20px' }}>
      {nodeDatum.attributes.DOB}
    </text>
    <text fill="#37672F" stroke="none" x="60" y="35" style={{ fontSize: '20px' }}>
      {nodeDatum.attributes.DOD}
    </text>
    <foreignObject x="-45" y="55" width="90" height="50">
      <input
        type="file"
        onChange={(event) => uploadImage(event, nodeDatum.id)}
        style={{ width: '90px' }}
      />
    </foreignObject>
    {spouses.length > 0 && (
      <g>
        <line
          x1={60}
          y1={0}
          x2={spouseSpacing - 50}
          y2={0}
          stroke="black"
          strokeWidth={2}
          strokeDasharray="5,5"
        />
        {spouses.length > 1 && (
          <line
            x1={spouseSpacing - 50}
            y1={-verticalSpacing / 3}
            x2={spouseSpacing - 50}
            y2={(spouses.length + 1.2) * verticalSpacing}
            stroke="black"
            strokeWidth={2}
            strokeDasharray="5,5"
          />
        )}
        {spouses.map((spouse, index) => (
          <Tippy key={spouse.id} content={toolTip}>
          <g transform={`translate(${spouseSpacing}, ${index * verticalSpacing * 3})`} onClick={(event) => handleSpouseClick(event, spouse)}>
            <line
              x1={0}
              y1={0}
              x2={0}
              y2={0}
              stroke="black"
              strokeWidth={2}
            />
            <circle r={50} style={{
              stroke: spouse.attributes.gender === 'M' ? '#97EBE6' : spouse.attributes.gender === 'F' ? '#EB97CF' : '#EBC097',
              fill: 'none',
              strokeWidth: 10,
            }} />
            <image
              href={spouse.attributes.image || '/images/user.png'}
              x="-50"
              y="-50"
              width="100"
              height="100"
              clipPath="url(#clipCircle)"
              ></image>
                <defs>
                  <clipPath id="clipCircle">
                    <circle cx="0" cy="0" r="50" />
                  </clipPath>
                </defs>
               <text fill="#37672F" stroke="none" x="60" y="-5" style={{ fontSize: '24px', fontFamily: 'Times New Roman' }}>
                {spouse.name}
              </text>
              <text fill="#37672F" stroke="none" x="60" y="15" style={{ fontSize: '20px' }}>
              {spouse.attributes.DOB}
             </text>
              <text fill="#37672F" stroke="none" x="60" y="35" style={{ fontSize: '20px' }}>
              {spouse.attributes.DOD}
              </text>
            </g>
          </Tippy>
            ))}
          </g>
          )}
       </g>
     </Tippy>
    </>
    );
  };

    if (!treeData) { //alternate display if no tree data is available - error message and search bar
      return <div>{errorMessage}
      <div style={{ margin: '10px', width: '100%', height: '100vh' }}>
      <input 
        type="text" 
        value={query} 
        onChange={(e) => setQuery(e.target.value)} 
        placeholder="Search a bloodline (surname)"
      />
      <button onClick={search}>Search</button>
      </div>
      </div>
    }
  return ( //utilises react-d3-tree library to set parameters for tree display
    //sets width and height of display, places search bar, the data to be used, any node customisations the orientation of the tree and style of links/branches, positioning of tree and spacing between sibling and non-sibling nodes
    //only appears if the user has searched a bloodline/surname where family tree data is available, and no errors were given
    //also ensures sidebar is only opened if isSidebarOpened is true, and if so will display the data of a selected node and also close if user selects to do this
    <div style={{ width: '100%', height: '100vh' }}>
      <input 
        type="text" 
        value={query} 
        onChange={(e) => setQuery(e.target.value)} 
        placeholder="Search a bloodline (surname)"
      />
      <button onClick={search}>Search</button>

      {hasSearched && treeData && !errorMessage && (
      <Tree
        data={treeData}
        orientation="vertical"
        pathFunc="step"
        translate={{ x: 300, y: 50 }}
        separation={{ siblings: 4, nonSiblings: 5}}
        nodeSize={{ x: 190, y: 300 }}
        renderCustomNodeElement={customNode}
      />
    )}
      {isSidebarOpened && <Sidebar node={selectedNode} onClose={closeSidebar} />}
    </div>
  );
};

export default FamilyTree; //exports component for use