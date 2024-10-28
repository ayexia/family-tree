//imports required react and custom hooks
import React, { useState, useEffect, useCallback, useMemo } from 'react';
//imports components and utilities from reactflow library for graph visualisation
import ReactFlow, { 
  Background, 
  useNodesState, 
  useEdgesState,
  Handle,
  Position,
  useReactFlow
} from 'reactflow';
//imports dagre library for graph layout calculations - https://github.com/dagrejs/dagre 
import dagre from '@dagrejs/dagre';
//imports required reactflow styling - https://reactflow.dev/ 
import 'reactflow/dist/style.css';
//used to make api requests
import axios from 'axios';
//imports sidebar component for displaying node details
import GraphSidebar from './GraphSidebar.jsx';
//imports cake icon for birthday display - https://lucide.dev/icons/cake 
import { Cake } from 'lucide-react';
//imports flag component for location display - https://github.com/lazicnemanja/react-country-flags
import ReactCountryFlag from 'react-country-flag';

//defines dimensions for graph nodes
const nodeWidth = 300;
const nodeHeight = 150;
//sets default profile image path
const defaultImage = '/images/user.png';

//maps cities to their country codes for flag display
const cityToCountryCode = {
  'New York': 'US',
  'Stratford-upon-Avon': 'GB',
  'Shottery, Warwickshire': 'GB',
  'Paris': 'FR',
};

//main graph component accepting props for configuration and state management
const FamilyGraph = ({ 
  generations, 
  desiredName,
  showStatistics,
  setShowStatistics,
  highlightedNode,
  setHighlightedNode,
  setSearchResults,
  setZoomIn,
  setZoomOut,
  setCenterView,
  lineStyles
}) => {
  //initialises states for nodes and their position changes
  const [nodes, setNodes, onNodesChange] = useNodesState([]);
  //initialises states for edges and their changes
  const [edges, setEdges, onEdgesChange] = useEdgesState([]);
  //controls sidebar visibility
  const [isSidebarOpened, setIsSidebarOpened] = useState(false);
  //tracks currently selected node
  const [selectedNode, setSelectedNode] = useState(null);
  //stores any error messages
  const [errorMessage, setErrorMessage] = useState('');
  //stores uploaded member images
  const [images, setImages] = useState({});
  //gets reactflow utilities for view manipulation
  const { setCenter, zoomIn: reactFlowZoomIn, zoomOut: reactFlowZoomOut, fitView } = useReactFlow();

  //checks if current date matches a member's birthday
const isBirthday = (birthDate) => {
  if (!birthDate) return false;
  const today = new Date();
  const [year, month, day] = birthDate.split('-').map(Number);

  const birthdayThisYear = new Date(today.getFullYear(), month - 1, day);

  return today.getMonth() === birthdayThisYear.getMonth() && today.getDate() === birthdayThisYear.getDate();
};

//defines custom node appearance and functionality for each person in tree
const customNode = useCallback(({ data }) => {
  //checks if today matches person's birth date for birthday display
  const isTodayBirthday = isBirthday(data.birth_date);
  //looks up country code based on birth place for flag display
  const countryCode = data.birth_place ? cityToCountryCode[data.birth_place] : null;
  //assigns colour to node based on person's gender (M/F/other)
  const nodeColour = data.gender === 'M' ? lineStyles.nodeMale.color : 
                    data.gender === 'F' ? lineStyles.nodeFemale.color : 
                    lineStyles.nodeOther.color;
  return (
  //container for person's node with styling
  <div style={{ 
      padding: 10, 
      borderRadius: 5, 
      background: nodeColour,
      border: '1px solid #ccc', 
      whiteSpace: 'pre-wrap', 
      textAlign: 'center',
      position: 'relative'
  }}>
      {isTodayBirthday && (
          //displays cake icon in top right if it's person's birthday
          <div style={{ position: 'absolute', top: 0, right: 10 }}>
              <Cake size={24} color="#FFD700" />
          </div>
      )}
      {countryCode && (
          //displays country flag in top left based on birth place
          <div style={{ position: 'absolute', top: 0, left: 10 }}>
              <ReactCountryFlag 
                  countryCode={countryCode}
                  svg 
                  style={{ width: '1.5em', height: '1.5em' }}
              />
          </div>
      )}
      <img src={data.image ? data.image : defaultImage} style={{ width: '50px', height: '50px', borderRadius: '25%' }} />
      <div>{data.label}</div>
      <Handle type="target" position={Position.Top} id="top" />
      <Handle type="source" position={Position.Bottom} id="bottom" />
      <Handle type="source" position={Position.Left} id="left" />
      <Handle type="source" position={Position.Right} id="right" />
  </div>
  );
}, [lineStyles]);

//prevents unnecessary rerenders of node types
const nodeTypes = useMemo(() => ({ custom: customNode }), [customNode]);

//calculates layout for entire family tree including positions and connections
const getLayoutedElements = (nodes, edges) => {
  //creates new graph structure for layout calculation
  const g = new dagre.graphlib.Graph().setDefaultEdgeLabel(() => ({}));
  //configures graph layout direction and spacing:
  //TB = top to bottom direction
  //ranksep = vertical space between generations
  //nodesep = horizontal space between family members
  g.setGraph({ rankdir: 'TB', ranksep: 300, nodesep: 450 });

  //sets size for each person's node in the graph
  nodes.forEach((node) => {
      g.setNode(node.id, { width: nodeWidth, height: nodeHeight });
  });

  //adds all parent-child connections to graph, excluding spouse connections
  edges.filter(edge => edge.label !== 'Spouse').forEach((edge) => {
      g.setEdge(edge.source, edge.target);
  });

  //runs dagre layout algorithm to calculate positions
  dagre.layout(g);

  //maps calculated positions to nodes while enabling drag functionality
  const positionedNodes = nodes.map((node) => {
      const position = g.node(node.id);
      return { ...node, position: { x: position.x, y: position.y }, draggable: true };
  });

  //handles spouse node positioning separately from main tree layout
  edges.filter(edge => edge.label === 'Spouse').forEach((edge, index) => {
      //finds both nodes in the spouse relationship
      const sourceNode = positionedNodes.find(node => node.id === edge.source);
      const targetNode = positionedNodes.find(node => node.id === edge.target);
      if (sourceNode && targetNode) {
          //calculates number of spouses for circular arrangement
          const numOfSpouses = edges.filter(e => e.source === edge.source && e.label === 'Spouse').length;
          //calculates angle for each spouse's position in circle
          const angle = (2 * Math.PI / numOfSpouses) * index;
          //determines x position using cosine
          const horizontalPosition = Math.cos(angle) * 200;
          //determines y position using sine
          const verticalPosition = Math.sin(angle) * 175;
    
        //sets final position for spouse node relative to main family member
        targetNode.position = {
          x: sourceNode.position.x + horizontalPosition,
          y: sourceNode.position.y + verticalPosition
      };
  }
});

return {
  //returns nodes with their calculated positions
  nodes: positionedNodes,
  //processes and returns edges with appropriate styling based on relationship type
  edges: edges.map(edge => {
      let style = {};
      //applies different styles for spouse vs parent-child relationships
      if (edge.label === 'Spouse') {
          //uses different styles for current vs divorced spouses
          style = edge.is_current ? lineStyles.current : lineStyles.divorced;
      } else {
          //uses different styles for adopted vs biological children
          style = edge.isAdopted ? lineStyles.adopted: lineStyles.parentChild;
      }
      return {
          ...edge,
          //applies calculated style to connection line
          style: {
              stroke: style.color,
              strokeWidth: style.width,
              strokeDasharray: style.dashArray,
          },
          //sets appropriate label text based on relationship type
          label: edge.label === 'Spouse' 
              ? (edge.is_current ? 'Spouse' : 'Divorced') 
              : edge.isAdopted ? 'Adopted Child' : 'Child',
      };
  })
};
};

  //fetches and processes family tree data from api
  const fetchFamilyTreeData = useCallback(async () => {
  try {
    //makes api request with current generation depth and search name
    const response = await axios.get('/api/family-graph-json', { params: { generations, desiredName } });
    //handles case where no family members are found
    if (response.data.nodes.length === 0) {
        setErrorMessage('No results found.');
        setNodes([]);
        setEdges([]);
    } else {
        //processes received data through layout calculator
        const { nodes: layoutedNodes, edges: layoutedEdges } = getLayoutedElements(
            response.data.nodes,
            response.data.edges
        );
        //updates nodes with any uploaded images
        setNodes(layoutedNodes.map(node => ({
            ...node,
            data: { ...node.data, image: images[node.id] || node.data.image }
        })));
        //sets processed relationship connections
        setEdges(layoutedEdges);
        //clears any existing error messages
        setErrorMessage('');
    }
  } catch (error) {
    //logs and displays error message if data fetch fails
    console.error('Error fetching data:', error);
    setErrorMessage('An error occurred while fetching data.');
  }
  }, [images, generations, lineStyles, desiredName]);

  //triggers data fetch when component mounts or dependencies change
  useEffect(() => {
  fetchFamilyTreeData();
  }, [fetchFamilyTreeData]);

  useEffect(() => {
    if (highlightedNode) {
      const node = nodes.find(n => n.id === highlightedNode);
      if (node) {
        setCenter(node.position.x, node.position.y, { zoom: 1.5, duration: 1000 });
        setHighlightedNode(null);
      }
    }
  }, [highlightedNode, nodes, setCenter, setHighlightedNode]);

  //manages centering view on highlighted node
useEffect(() => {
  if (highlightedNode) {
      //finds the highlighted node in current nodes
      const node = nodes.find(n => n.id === highlightedNode);
      if (node) {
          //centers view on node with zoom animation
          setCenter(node.position.x, node.position.y, { zoom: 1.5, duration: 1000 });
          //clears highlight after centering
          setHighlightedNode(null);
      }
  }
}, [highlightedNode, nodes, setCenter, setHighlightedNode]);

  //sets up zoom and view control functions
  useEffect(() => {
    //assigns zoom in functionality
    setZoomIn(() => () => reactFlowZoomIn());
    //assigns zoom out functionality
    setZoomOut(() => () => reactFlowZoomOut());
    //assigns view centering functionality with animation
    setCenterView(() => () => fitView({ duration: 800, padding: 0.1 }));
  }, [setZoomIn, setZoomOut, setCenterView, reactFlowZoomIn, reactFlowZoomOut, fitView]);

  //handles search functionality
  const handleSearch = useCallback(() => {
    //filters nodes based on search term
    const results = nodes.filter(node => 
        node.data.label.toLowerCase().includes(desiredName.toLowerCase())
    );
    //updates search results state
    setSearchResults(results);
  }, [desiredName, nodes, setSearchResults]);

  //triggers search when search term changes
  useEffect(() => {
    handleSearch();
  }, [handleSearch]);

  //opens sidebar when node is clicked
  const openSidebar = (node) => {
    //sets clicked node as selected
    setSelectedNode(node);
    //displays sidebar
    setIsSidebarOpened(true);
  };

  //handles closing of sidebar
  const closeSidebar = () => {
    //hides sidebar
    setIsSidebarOpened(false);
    //clears selected node
    setSelectedNode(null);
  };

  //handles node click events
  const onNodeClick = (event, node) => {
    try {      
        //attempts to open sidebar for clicked node
        openSidebar(node);
    } catch (error) {
        //logs error if sidebar opening fails
        console.error('Error handling node click:', error);
    }
  };

  //calculates family statistics
  const statistics = useMemo(() => {
    return {
        //counts total family members
        totalMembers: nodes.length,
        //counts male family members
        maleCount: nodes.filter(node => node.data.gender === 'M').length,
        //counts female family members
        femaleCount: nodes.filter(node => node.data.gender === 'F').length,
        //counts members with other/unknown gender
        unknownCount: nodes.filter(node => node.data.gender !== 'M' && node.data.gender !== 'F').length
    };
  }, [nodes]);

//styling for statistics popup
const popupStyle = {
  position: 'fixed',
  top: '50%',
  left: '50%',
  transform: 'translate(-50%, -50%)',
  backgroundColor: 'white',
  padding: '20px',
  borderRadius: '10px',
  boxShadow: '0 0 10px rgba(0,0,0,0.2)',
  zIndex: 1000,
};

  //returns main component structure
return (
  //container for entire family graph
  <div style={{ width: '100%', height: '100%', position: 'relative' }}>
      {errorMessage && 
          //displays error message if one exists
          <p style={{ position: 'absolute', top: 10, left: 10, color: 'red' }}>
              {errorMessage}
          </p>
      }
      
      <ReactFlow
          //provides node data and positions
          nodes={nodes}
          //provides relationship connections
          edges={edges}
          //handles node position updates
          onNodesChange={onNodesChange}
          //handles relationship updates
          onEdgesChange={onEdgesChange}
          //handles node selection
          onNodeClick={onNodeClick}
          //automatically fits graph to view
          fitView
          //applies custom node styling
          nodeTypes={nodeTypes}
          //sets minimum zoom level
          minZoom={0.01}
          //sets maximum zoom level
          maxZoom={2}
      >
          <Background variant="dots" gap={12} size={1} />
      </ReactFlow>

      {showStatistics && (
          //displays statistics popup when enabled
          <div style={popupStyle}>
              <h3>Family Statistics</h3>
              <p>Total Members: {statistics.totalMembers}</p>
              <p>Male: {statistics.maleCount}</p>
              <p>Female: {statistics.femaleCount}</p>
              <p>Other: {statistics.unknownCount}</p>
              <button onClick={() => setShowStatistics(false)}>Close</button>
          </div>
      )}

      {isSidebarOpened && 
          //displays sidebar when node is selected
          <GraphSidebar 
              node={selectedNode} 
              onClose={closeSidebar} 
              setImages={setImages} 
              images={images} 
          />
      }
  </div>
);
};

//exports component for use in other parts of application
export default FamilyGraph;