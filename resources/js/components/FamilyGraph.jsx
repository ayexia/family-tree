import React, { useState, useEffect, useCallback, useMemo } from 'react';
import ReactFlow, { 
  Background, 
  useNodesState, 
  useEdgesState,
  Handle,
  Position,
  useReactFlow
} from 'reactflow';
import dagre from '@dagrejs/dagre';
import 'reactflow/dist/style.css';
import axios from 'axios';
import GraphSidebar from './GraphSidebar.jsx';
import { Cake } from 'lucide-react';
import ReactCountryFlag from 'react-country-flag';

const nodeWidth = 300;
const nodeHeight = 150;
const defaultImage = '/images/user.png';

const cityToCountryCode = {
  'New York': 'US',
  'Stratford-upon-Avon': 'GB',
  'Shottery, Warwickshire': 'GB',
  'Paris': 'FR',
};

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
  const [nodes, setNodes, onNodesChange] = useNodesState([]);
  const [edges, setEdges, onEdgesChange] = useEdgesState([]);
  const [isSidebarOpened, setIsSidebarOpened] = useState(false);
  const [selectedNode, setSelectedNode] = useState(null);
  const [errorMessage, setErrorMessage] = useState('');
  const [images, setImages] = useState({});
  const { setCenter, zoomIn: reactFlowZoomIn, zoomOut: reactFlowZoomOut, fitView } = useReactFlow();

  const isBirthday = (birthDate) => {
    if (!birthDate) return false;
    const today = new Date();
    const [year, month, day] = birthDate.split('-').map(Number);
  
    const birthdayThisYear = new Date(today.getFullYear(), month - 1, day);
  
    return today.getMonth() === birthdayThisYear.getMonth() && today.getDate() === birthdayThisYear.getDate();
  };

  const customNode = useCallback(({ data }) => {
    const isTodayBirthday = isBirthday(data.birth_date);
    const countryCode = data.birth_place ? cityToCountryCode[data.birth_place] : null;
    const nodeColour = data.gender === 'M' ? lineStyles.nodeMale.color : 
                      data.gender === 'F' ? lineStyles.nodeFemale.color : 
                      lineStyles.nodeOther.color;
    return (
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
          <div style={{ position: 'absolute', top: 0, right: 10 }}>
            <Cake size={24} color="#FFD700" />
          </div>
        )}
         {countryCode && (
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

  const nodeTypes = useMemo(() => ({ custom: customNode }), [customNode]);

  const getLayoutedElements = (nodes, edges) => {
    const g = new dagre.graphlib.Graph().setDefaultEdgeLabel(() => ({}));
    g.setGraph({ rankdir: 'TB', ranksep: 300, nodesep: 450 });

    nodes.forEach((node) => {
      g.setNode(node.id, { width: nodeWidth, height: nodeHeight });
    });

    edges.filter(edge => edge.label !== 'Spouse').forEach((edge) => {
      g.setEdge(edge.source, edge.target);
    });

    dagre.layout(g);

    const positionedNodes = nodes.map((node) => {
      const position = g.node(node.id);
      return { ...node, position: { x: position.x, y: position.y }, draggable: true };
    });

    edges.filter(edge => edge.label === 'Spouse').forEach((edge, index) => {
      const sourceNode = positionedNodes.find(node => node.id === edge.source);
      const targetNode = positionedNodes.find(node => node.id === edge.target);
      if (sourceNode && targetNode) {
        const numOfSpouses = edges.filter(e => e.source === edge.source && e.label === 'Spouse').length;
        const angle = (2 * Math.PI / numOfSpouses) * index;
        const horizontalPosition = Math.cos(angle) * 200;
        const verticalPosition = Math.sin(angle) * 175;
    
        targetNode.position = {
          x: sourceNode.position.x + horizontalPosition,
          y: sourceNode.position.y + verticalPosition
        };
      }
    });

    return {
      nodes: positionedNodes,
      edges: edges.map(edge => {
        let style = {};
        if (edge.label === 'Spouse') {
          style = edge.is_current ? lineStyles.current : lineStyles.divorced;
        } else {
          style = edge.isAdopted ? lineStyles.adopted: lineStyles.parentChild;
        }
        return {
          ...edge,
          style: {
            stroke: style.color,
            strokeWidth: style.width,
            strokeDasharray: style.dashArray,
          },
          label: edge.label === 'Spouse' 
            ? (edge.is_current ? 'Spouse' : 'Divorced') 
            : edge.isAdopted ? 'Adopted Child' : 'Child',
        };
      })
    };
  };

  const fetchFamilyTreeData = useCallback(async () => {
    try {
      const response = await axios.get('/api/family-graph-json', { params: { generations, desiredName } });
      if (response.data.nodes.length === 0) {
        setErrorMessage('No results found.');
        setNodes([]);
        setEdges([]);
      } else {
        const { nodes: layoutedNodes, edges: layoutedEdges } = getLayoutedElements(
          response.data.nodes,
          response.data.edges
        );
        setNodes(layoutedNodes.map(node => ({
          ...node,
          data: { ...node.data, image: images[node.id] || node.data.image }
        })));
        setEdges(layoutedEdges);
        setErrorMessage('');
      }
    } catch (error) {
      console.error('Error fetching data:', error);
      setErrorMessage('An error occurred while fetching data.');
    }
  }, [images, generations, lineStyles, desiredName]);

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

  useEffect(() => {
    setZoomIn(() => () => reactFlowZoomIn());
    setZoomOut(() => () => reactFlowZoomOut());
    setCenterView(() => () => fitView({ duration: 800, padding: 0.1 }));
  }, [setZoomIn, setZoomOut, setCenterView, reactFlowZoomIn, reactFlowZoomOut, fitView]);

  const handleSearch = useCallback(() => {
    const results = nodes.filter(node => 
      node.data.label.toLowerCase().includes(desiredName.toLowerCase())
    );
    setSearchResults(results);
  }, [desiredName, nodes, setSearchResults]);

  useEffect(() => {
    handleSearch();
  }, [handleSearch]);

  const openSidebar = (node) => {
    setSelectedNode(node);
    setIsSidebarOpened(true);
  };

  const closeSidebar = () => {
    setIsSidebarOpened(false);
    setSelectedNode(null);
  };

  const onNodeClick = (event, node) => {
    try {      
      openSidebar(node);
    } catch (error) {
      console.error('Error handling node click:', error);
    }
  };

  const statistics = useMemo(() => {
    return {
      totalMembers: nodes.length,
      maleCount: nodes.filter(node => node.data.gender === 'M').length,
      femaleCount: nodes.filter(node => node.data.gender === 'F').length,
      unknownCount: nodes.filter(node => node.data.gender !== 'M' && node.data.gender !== 'F').length
    };
  }, [nodes]);

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

  return (
    <div style={{ width: '100%', height: '100%', position: 'relative' }}>
      {errorMessage && <p style={{ position: 'absolute', top: 10, left: 10, color: 'red' }}>{errorMessage}</p>}
      
      <ReactFlow
        nodes={nodes}
        edges={edges}
        onNodesChange={onNodesChange}
        onEdgesChange={onEdgesChange}
        onNodeClick={onNodeClick}
        fitView
        nodeTypes={nodeTypes}
        minZoom={0.01}
        maxZoom={2}
      >
        <Background variant="dots" gap={12} size={1} />
      </ReactFlow>

      {showStatistics && (
        <div style={popupStyle}>
          <h3>Family Statistics</h3>
          <p>Total Members: {statistics.totalMembers}</p>
          <p>Male: {statistics.maleCount}</p>
          <p>Female: {statistics.femaleCount}</p>
          <p>Other: {statistics.unknownCount}</p>
          <button onClick={() => setShowStatistics(false)}>Close</button>
        </div>
      )}

      {isSidebarOpened && <GraphSidebar node={selectedNode} onClose={closeSidebar} setImages={setImages} images={images} />}
    </div>
  );
};

export default FamilyGraph;