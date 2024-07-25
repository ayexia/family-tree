// import React, { useEffect, useState, useRef } from 'react'; //react modules handling states and effects of components, and accessing DOM elements (e.g. HTML elements such as div, body etc)
// import axios from 'axios';
// import * as d3 from 'd3';
// import "../../css/treeCustomisation.css";

// function FamilyTree() {
//   const [treeData, setTreeData] = useState(null);
//   const svgRef = useRef(null); //initialises reference for <svg> element (where visualisation will be rendered)

//   useEffect(() => {
//     axios.get('/api/family-tree-json')
//       .then(response => {
//         setTreeData(response.data[25]);
//       })
//       .catch(error => console.error('Error fetching data:', error));
//   }, []);

//   useEffect(() => {
//     if (treeData) {
//       createTree(treeData, svgRef.current); //runs createTree method whenever the data in treeData changes, only if treeData is available
//     }
//   }, [treeData]);

//   function createTree(data, svgElement) {
//     const width = document.body.clientWidth; //sets display width to full width (displaying tree)
//     const height = 1000; //sets height for tree
//     const margin = { top: 20, right: 20, bottom: 30, left: 200 }; //sets margins for canvas
//     const spouseSpacing = 60; //sets space between spouses
//     const spouseLinkLength = 60; //sets link length between spouses
  
//     d3.select(svgElement).selectAll("*").remove(); //prepares for new tree drawing by clearing anything currently existing in svgElement
  
//     const svg = d3.select(svgElement) //selects svgElement for displaying tree, sets width and height of SVG canvas, appends and groups all tree elements together and positions them within the canvas (ensures drawing starts from top left of canvas)
//       .attr('width', width + margin.left + margin.right)
//       .attr('height', height + margin.top + margin.bottom)
//       .append('g')
//       .attr('transform', `translate(${margin.left},${margin.top})`);
  
//     const treeLayout = d3.tree().size([height, width - 300]); //creates new tree layout and sets dimensions where nodes will be placed
  
//     const root = d3.hierarchy(data); //creates root node from fetched data
//     treeLayout(root); //creates tree layout for the root node
  
//     root.each(d => {
//       if (d.data.spouses) { //if current node has spouses count number of spouses, multiplies this by spacing then calculate where spouse nodes are positioned (ideally main node centred - needs reworking)
//         const spouseCount = d.data.spouses.length;
//         const totalWidth = spouseCount * spouseSpacing;
//         d.x -= totalWidth / 2;
  
//         d.data.spouses.forEach((spouse, i) => { //iterates through each spouse
//           const spouseNode = root.copy(); //creates copy node of the root and stores spouse data
//           spouseNode.data = spouse;
//           spouseNode.depth = d.depth; //places in same position/hierarchy of current node
//           spouseNode.height = 0;
//           spouseNode.parent = d; //sets current node as parent ensuring a link between the two
//           spouseNode.x = d.x + (i + 1) * spouseSpacing; //sets positioning of node relative to current node, based on its index (allowing for spacing between multiple spouses)
//           spouseNode.y = d.y;
//           if (!d.spouseNodes) d.spouseNodes = []; //if array doesnt exist initialise array to store spouses for current node
//           d.spouseNodes.push(spouseNode);
//         });
//       }
//     });
  
//     const allNodes = root.descendants().concat(
//       root.descendants().flatMap(d => d.spouseNodes || []) //retrieves all nodes of root's hierarchy, and concatenates descendants with the spouse arrays of each descendant (flattened into one array via flatMap, no spouses returns an empty array for that descendant)
//     );

//     const linkPathGenerator = d3.linkHorizontal() //generates horizontal link between nodes and sets positioning
//     .x(d => d.y)
//     .y(d => d.x); 

//     const link = svg.selectAll('.link') //selects all links for customisation
//       .data(root.links()) //returns array of links between nodes and stores these
//       .enter().append('path') //creates a path between each link
//       .attr('class', 'link') //sets CSS class
//       .attr('d', linkPathGenerator) //defines how to draw path
//       .style('fill', 'none')
//       .style('stroke', '#fff')
//       .style('stroke-width', '2px');
  
//     const spouseLink = svg.selectAll('.spouse-link') //selects all spouse links for customisation
//       .data(root.descendants().filter(d => d.spouseNodes)) //retrieves spouse nodes to store for spouse links
//       .enter().append('path')
//       .attr('class', 'spouse-link')
//       .attr('d', d => {
//         const start = d.y; // sets starting position (y coordinate of current node)
//         const end = d.spouseNodes[d.spouseNodes.length - 1].y; //sets end position (y coordinate of last spouse in array)
//         const midX = (start + end) / 2; //sets midpoint
//         const bottomY = d.x + spouseLinkLength; //sets position where line ends (x coordinate of current node + length of spouse link)
//         //moves to start position, draws horizontal line to midpoint, draws vertical line downwards, draws horizontal line to end position, draws vertical line to last spouse node
//         return `M${start},${d.x} 
//                 H${midX} 
//                 V${bottomY}
//                 H${end}
//                 V${d.spouseNodes[d.spouseNodes.length - 1].x}`;
//       })
//       .style('fill', 'none')
//       .style('stroke', '#ffc')
//       .style('stroke-width', '2px')
//       .style('stroke-dasharray', '5,5');
  
//     const node = svg.selectAll('.node')//selects nodes for customisation
//       .data(allNodes) //stores all node information
//       .enter().append('g') //creates new group element for each node
//       .attr('class', d => `node ${d.children ? 'node--internal' : 'node--leaf'}`) //determines CSS style class depending on whether node has children or not
//       .attr('transform', d => `translate(${d.y},${d.x})`); //positions each node
  
//     node.append('circle') //styles node shape (circle)
//       .attr('r', 10)
//       .style('fill', '#fff')
//       .style('stroke', '#f80')
//       .style('stroke-width', '5px');
  
//     node.append('text') //styles text with positioning and sets text to display
//       .attr('dy', '0.32em')
//       .attr('x', d => d.children ? 100 : -10) //leaf node text position and alignment on right, non-leaf on left
//       .attr('y', -20)
//       .style('text-anchor', d => d.children ? 'end' : 'start')
//       .text(d => d.data.name)
//       .style('font-size', '12px')
//       .style('fill', '#333');
//   }
//   return ( //returns display
//     <div>
//       <h1>Family Tree</h1>
//       <div className="tree-container">
//         <svg ref={svgRef}></svg>
//       </div>
//     </div>
//   );
// }

// export default FamilyTree;

import React, { useState, useEffect } from 'react'; //react modules handling states and effects of components
import Tree from 'react-d3-tree'; //Uses react-d3-tree package for visual representation of tree structure
import axios from 'axios'; //used to fetch api data through making http requests
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import "../../css/treeCustomisation.css";
import Sidebar from './Sidebar';

const FamilyTree = () => {
  const [treeData, setTreeData] = useState(null); //initialises variable treeData
  const [setImages] = useState({});
  const [ isSidebarOpened, setIsSidebarOpened ] = useState( false );
  const [selectedNode, setSelectedNode] = useState(null);

  useEffect(() => {
    fetchFamilyTreeData(); //after component is mounted calls this function which retrieves the family tree data from the api through http requests
  }, []);

  const fetchFamilyTreeData = async () => {
    try {
      const response = await axios.get('/api/family-tree-json'); //uses axios library to make http request to fetch api data, which is then parsed as JSON
      setTreeData(response.data); //the fetched data is stored in treeData variable
    } catch (error) {
      console.error('Error fetching family tree data:', error); //if any issues with retrieving data will print error message
    }
  };

  const uploadImage = async (event, node) => {
    const selectedFile = event.target.files[0];
    if (!selectedFile) return;
  
    const formData = new FormData();
    formData.append('image', selectedFile);
    formData.append('id', node);
  
    try {
      const response = await axios.post('/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      setImages((prevImages) => ({ ...prevImages, [node]: response.data.imagePath }));
    } catch (error) {
      console.error('Error uploading image:', error);
    }
  };
  
  const openSidebar = (node) => {
      setSelectedNode(node);
      setIsSidebarOpened(true); 
    };

  const closeSidebar = () => {
      setIsSidebarOpened(false);
      setSelectedNode(null); 
    };


  const customNode = ({ nodeDatum }) => {
    const selectedImage = nodeDatum.attributes.image || '/images/user.png';
    const isMale = nodeDatum.attributes.gender === 'M';
    const isFemale = nodeDatum.attributes.gender === 'F';
    const nodeStyle = {
      stroke: isMale ? '#97EBE6' : isFemale ? '#EB97CF': '#EBC097',
      fill: 'none',
      strokeWidth: 10,
    };

  const toolTip = (
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

    return (
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
        <text fill="#B2BEB5" stroke="none" x="60" y="-5" style={{ fontSize: '24px', fontFamily: 'Times New Roman' }}>
          {nodeDatum.name}
        </text>
        <text fill="#B2BEB5" stroke="none" x="60" y="15" style={{ fontSize: '20px' }}>
          {nodeDatum.attributes.DOB}
        </text>
        <text fill="#B2BEB5" stroke="none" x="60" y="35" style={{ fontSize: '20px' }}>
          {nodeDatum.attributes.DOD}
        </text>
        <foreignObject x="-45" y="55" width="90" height="50">
          <input
            type="file"
            onChange={(event) => uploadImage(event, nodeDatum.id)}
            style={{ width: '90px' }}
          />
        </foreignObject>
      </g>
      </Tippy>
    );
  };

    if (!treeData) {
      return <div>Loading...</div>; //alternate display if no tree data is available
    }
  return ( //utilises react-d3-tree library to set parameters for tree display
    //sets width and height of display, the data to be used, the orientation of the tree and style of links/branches, positioning of tree and spacing between sibling and non-sibling nodes
    <div style={{ width: '100%', height: '100vh' }}>
      <Tree
        data={treeData}
        orientation="vertical"
        pathFunc="step"
        translate={{ x: 300, y: 50 }}
        separation={{ siblings: 4.8, nonSiblings: 5}}
        nodeSize={{ x: 190, y: 300 }}
        renderCustomNodeElement={customNode}
      />
      {isSidebarOpened && <Sidebar node={selectedNode} onClose={closeSidebar} />}
    </div>
  );
};

export default FamilyTree; //exports component for use