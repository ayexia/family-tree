import React, { useState, useEffect, useCallback, useMemo } from 'react';
import ReactFlow, { 
  MiniMap, 
  Controls, 
  Background, 
  useNodesState, 
  useEdgesState,
  Handle,
  Position 
} from 'reactflow';
import dagre from '@dagrejs/dagre';
import 'reactflow/dist/style.css';
import axios from 'axios';
import GraphSidebar from './GraphSidebar.jsx';

const nodeWidth = 150;
const nodeHeight = 50;
const defaultImage = '/images/user.png';

const customNode = ({ data }) => (
  <div style={{ padding: 10, borderRadius: 5, background: '#fff', border: '1px solid #ccc', whiteSpace: 'pre-wrap', textAlign: 'center'}}>
    <img src={data.image ? data.image : defaultImage} style={{ width: '35px', height: '35px', borderRadius: '25%' }} />
    <div>{data.label}</div>
    <Handle type="target" position={Position.Top} id="top" />
    <Handle type="source" position={Position.Bottom} id="bottom" />
    <Handle type="source" position={Position.Left} id="left" />
    <Handle type="source" position={Position.Right} id="right" />
  </div>
);

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

const FamilyGraph = () => {
  const [nodes, setNodes, onNodesChange] = useNodesState([]);
  const [edges, setEdges, onEdgesChange] = useEdgesState([]);
  const [isSidebarOpened, setIsSidebarOpened] = useState(false);
  const [selectedNode, setSelectedNode] = useState(null);
  const [errorMessage, setErrorMessage] = useState('');
  const [images, setImages] = useState({});
  const [generations, setGenerations] = useState(3);

  const nodeTypes = useMemo(() => ({ custom: customNode }), []);

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
  }, [fetchFamilyTreeData, images]);

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

  return (
    <div style={{ width: '100%', height: '600px' }}>
      {errorMessage && <p>{errorMessage}</p>}
      <div style={{ marginBottom: '10px' }}>
        <label htmlFor="generations">Select Generation: </label>
        <input
          type="number"
          id="generations"
          value={generations}
          onChange={(e) => setGenerations(e.target.value)}
          min="1"
          max="10"
        />
      </div>
      <ReactFlow
        nodes={nodes}
        edges={edges}
        onNodesChange={onNodesChange}
        onEdgesChange={onEdgesChange}
        onNodeClick={onNodeClick}
        fitView={true}
        nodeTypes={nodeTypes}
        minZoom={0.01}
      >
        <Controls />
        <MiniMap />
        <Background variant="dots" gap={12} size={1} />
      </ReactFlow>
      {isSidebarOpened && <GraphSidebar node={selectedNode} onClose={closeSidebar} setImages={setImages} images={images} />}
    </div>
  );
};

export default FamilyGraph;