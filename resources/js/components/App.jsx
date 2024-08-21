import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import { ReactFlowProvider } from 'reactflow';
import FamilyTree from './FamilyTree';
import FamilyGraph from './FamilyGraph';
import FamilyTreePDF from './FamilyTreePDF';

const App = () => {
    const [view, setView] = useState('graph');
    const [showPDF, setShowPDF] = useState(false);
    const [generations, setGenerations] = useState(3);
    const [query, setQuery] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [highlightedNode, setHighlightedNode] = useState(null);
    const [showStatistics, setShowStatistics] = useState(false);

    const switchView = () => {
        setView(view === 'graph' ? 'tree' : 'graph');
    };

    const exportToPDF = () => {
        setShowPDF(true);
    };

    const resultSelect = (event) => {
        const selectedNodeId = event.target.value;
        setHighlightedNode(selectedNodeId);
    };

    // Placeholder functions for zoom and center view
    const zoomIn = () => {
        // TODO: Implement zoom in functionality
    };

    const zoomOut = () => {
        // TODO: Implement zoom out functionality
    };

    const centerView = () => {
        // TODO: Implement center view functionality
    };

    const buttonStyle = {
        backgroundColor: '#CCE7BD',
        color: '#A7B492',
        padding: '10px 20px',
        border: 'none',
        borderRadius: '50px',
        cursor: 'pointer',
        fontSize: '0.85em',
        fontFamily: '"Inika", serif',
        fontWeight: 'bold',
        transition: 'background-color 0.3s',
        margin: '10px 0',
        display: 'flex',
        alignItems: 'center',
        gap: '0px',
        width: '120%',
    };

    const imgStyle = {
        width: '25px',
        height: '25px',
        opacity: 0.3,
    };

    const controlsContainer = {
        width: '150px',
        height: '100vh',
        padding: '20px',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'flex-start',
        alignItems: 'stretch',
        position: 'absolute',
        left: -15,
        top: -22,
    };

    const mainContentStyle = {
        marginLeft: '165px',
        marginTop: '00px',
        width: 'calc(100vw - 165px)',
        height: '100vh',
        overflow: 'hidden',
    };

    const inputStyle = {
        width: '100%',
        padding: '10px',
        margin: '10px 0',
        borderRadius: '5px',
        border: '1px solid #ccc',
    };

    return (
        <ReactFlowProvider>
            <Router>
                <div style={{ display: 'flex' }}>
                    <div style={controlsContainer}>
                        <button style={buttonStyle} onClick={switchView}>
                            <img src="/images/grid.png" alt="Grid" style={imgStyle} />
                            Switch to {view === 'graph' ? 'Tree View' : 'Graph View'}
                        </button>
                        <button style={buttonStyle} onClick={exportToPDF}>
                            <img src="/images/printing.png" alt="PDF" style={imgStyle} />
                            Export to PDF book
                        </button>
                        <div>
                            <label htmlFor="generations">Generations: </label>
                            <input
                                type="number"
                                id="generations"
                                value={generations}
                                onChange={(e) => setGenerations(Number(e.target.value))}
                                min="1"
                                max="10"
                                style={inputStyle}
                            />
                        </div>
                        <div>
                            <input
                                type="text"
                                placeholder="Search for a person"
                                value={query}
                                onChange={(e) => setQuery(e.target.value)}
                                style={inputStyle}
                            />
                            <select 
                                onChange={resultSelect}
                                value={highlightedNode || ''}
                                style={inputStyle}
                            >
                                <option value="">Select a person</option>
                                {searchResults.map(node => (
                                    <option key={node.id} value={node.id}>
                                        {node.data.label}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <button style={buttonStyle} onClick={() => setShowStatistics(true)}>Show Statistics</button>
                        <div>
                            <button style={buttonStyle} onClick={zoomIn}>Zoom In</button>
                            <button style={buttonStyle} onClick={zoomOut}>Zoom Out</button>
                            <button style={buttonStyle} onClick={centerView}>Center View</button>
                        </div>
                    </div>
                    <div style={mainContentStyle}>
                        {view === 'graph' ? 
                            <FamilyGraph 
                                generations={generations} 
                                query={query} 
                                showStatistics={showStatistics}
                                setShowStatistics={setShowStatistics}
                                highlightedNode={highlightedNode}
                                setSearchResults={setSearchResults}
                                setHighlightedNode={setHighlightedNode}
                            /> : 
                            <FamilyTree 
                                generations={generations} 
                                query={query}
                                showStatistics={showStatistics}
                                setShowStatistics={setShowStatistics}
                                highlightedNode={highlightedNode}
                                setSearchResults={setSearchResults}
                            />
                        }
                    </div>
                </div>
                <Routes>
                    <Route path="/family-tree" element={<FamilyTree />} />
                    <Route path="/family-graph" element={<FamilyGraph />} />
                </Routes>
                {showPDF && <FamilyTreePDF onClose={() => setShowPDF(false)} />}
            </Router>
        </ReactFlowProvider>
    );
};

export default App;