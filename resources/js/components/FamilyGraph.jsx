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
import 'reactflow/dist/style.css';
import axios from 'axios';
import Sidebar from './Sidebar';

const customNode = ({ data }) => (
    <div style={{ padding: 10, borderRadius: 5, background: '#fff', border: '1px solid #ccc' }}>
      {data.label}
      <Handle type="target" position={Position.Top} id="a" style={{ background: '#555' }} />
      <Handle type="source" position={Position.Bottom} id="b" style={{ background: '#555' }} />
      <Handle type="source" position={Position.Left} id="c" style={{ background: '#555' }} />
      <Handle type="source" position={Position.Right} id="d" style={{ background: '#555' }} />
    </div>
  );

const FamilyGraph = () => {
  const [nodes, setNodes, onNodesChange] = useNodesState([]);
  const [edges, setEdges, onEdgesChange] = useEdgesState([]);
  const [isSidebarOpened, setIsSidebarOpened] = useState(false);
  const [selectedNode, setSelectedNode] = useState(null);
  const [errorMessage, setErrorMessage] = useState('');

  const nodeTypes = useMemo(() => ({ custom: customNode }), []);

  const fetchFamilyTreeData = useCallback(async () => {
    try {
      const response = await axios.get('/api/family-graph-json');
      if (response.data.nodes.length === 0) {
        setErrorMessage('No results found.');
        setNodes([]);
        setEdges([]);
      } else {
        setNodes(response.data.nodes);
        setEdges(response.data.edges);
        setErrorMessage('');
      }
    } catch (error) {
      console.error('Error fetching data:', error);
      setErrorMessage('An error occurred while fetching data.');
    }
  }, []);

  useEffect(() => {
    fetchFamilyTreeData();
  }, [fetchFamilyTreeData]);

  const openSidebar = (node) => {
    setSelectedNode(node);
    setIsSidebarOpened(true);
  };

  const closeSidebar = () => {
    setIsSidebarOpened(false);
    setSelectedNode(null);
  };

  const onNodeClick = (event, node) => {
    openSidebar(node);
  };

  return (
    <div style={{ width: '100%', height: '600px' }}>
      {errorMessage && <p>{errorMessage}</p>}
      <ReactFlow
        nodes={nodes}
        edges={edges}
        onNodesChange={onNodesChange}
        onEdgesChange={onEdgesChange}
        onNodeClick={onNodeClick}
        fitView
        nodeTypes={nodeTypes}
      >
        <Controls />
        <MiniMap />
        <Background variant="dots" gap={12} size={1} />
      </ReactFlow>
      {isSidebarOpened && <Sidebar node={selectedNode} onClose={closeSidebar} />}
    </div>
  );
};

export default FamilyGraph;