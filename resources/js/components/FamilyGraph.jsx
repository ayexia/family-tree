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

const nodeWidth = 150;
const nodeHeight = 50;
const defaultImage = '/images/user.png';

const FamilyGraph = ({ 
  generations, 
  query, 
  showStatistics,
  setShowStatistics,
  highlightedNode,
  setHighlightedNode,
  setSearchResults,
  setZoomIn,
  setZoomOut,
  setCenterView
}) => {
  const [nodes, setNodes, onNodesChange] = useNodesState([]);
  const [edges, setEdges, onEdgesChange] = useEdgesState([]);
  const [isSidebarOpened, setIsSidebarOpened] = useState(false);
  const [selectedNode, setSelectedNode] = useState(null);
  const [errorMessage, setErrorMessage] = useState('');
  const [images, setImages] = useState({});
  const { setCenter, zoomIn: reactFlowZoomIn, zoomOut: reactFlowZoomOut, fitView } = useReactFlow();

  const isBirthday = (dob) => {
    if (!dob) return false;
    const today = new Date();
    const birthDate = new Date(dob);
    return today.getMonth() === birthDate.getMonth() && today.getDate() === birthDate.getDate();
  };

  const customNode = useCallback(({ data }) => {
    const isTodayBirthday = isBirthday(data.birth_date);
    return (
    <div style={{ 
      padding: 10, 
      borderRadius: 5, 
      background: data.gender === 'M' ? '#97EBE6' : data.gender === 'F' ? '#EB97CF' : '#EBC097', 
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
      <img src={data.image ? data.image : defaultImage} style={{ width: '35px', height: '35px', borderRadius: '25%' }} />
         <div>{data.label}</div>
      <Handle type="target" position={Position.Top} id="top" />
      <Handle type="source" position={Position.Bottom} id="bottom" />
      <Handle type="source" position={Position.Left} id="left" />
      <Handle type="source" position={Position.Right} id="right" />
    </div>
  );
 }, []);

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
      edges: edges.map(edge => ({
        ...edge,
        style: {
          stroke: edge.label === 'Spouse' ? (edge.is_current ? 'red' : 'blue') : '#000000',
          strokeWidth: edge.label === 'Spouse' ? '0.5' : '0.2',
          strokeDasharray: edge.label === 'Spouse' ? (edge.is_current ? 'none' : '5,5') : 'none',             
        },
        label: edge.label === 'Spouse' ? (edge.is_current ? 'Spouse' : 'Former Spouse') : edge.label,
      }))
    };
  };

  const fetchFamilyTreeData = useCallback(async () => {
    try {
      const response = await axios.get('/api/family-graph-json', { params: { generations } });
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
  }, [images, generations]);

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
      node.data.label.toLowerCase().includes(query.toLowerCase())
    );
    setSearchResults(results);
  }, [query, nodes, setSearchResults]);

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
        minZoom={0.1}
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