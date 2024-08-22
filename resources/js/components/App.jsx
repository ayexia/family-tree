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
    const [zoomIn, setZoomIn] = useState(() => {});
    const [zoomOut, setZoomOut] = useState(() => {});
    const [centerView, setCenterView] = useState(() => {});
    const [lineStyles, setLineStyles] = useState({
        parentChild: { color: '#000000', width: 2, dashArray: 'none' },
        current: { color: '#FF0000', width: 2, dashArray: 'none' },
        divorced: { color: '#808080', width: 2, dashArray: '5,5' }
    });

    const handleLineStyleChange = (type, property) => (event) => {
        setLineStyles(prevStyles => ({
            ...prevStyles,
            [type]: {
                ...prevStyles[type],
                [property]: event.target.value
            }
        }));
    };

    const handleZoomIn = () => zoomIn();
    const handleZoomOut = () => zoomOut();
    const handleCenterView = () => centerView();

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

    const formatOptions = (results) => {
        return results.map(node => ({
            id: node.id,
            name: view === 'graph' ? node.data.label : node.name
        }));
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
        width: '100%',
    };

    const imgStyle = {
        width: '25px',
        height: '25px',
        opacity: 0.3,
    };

    const controlsContainer = {
        width: '160px',
        height: '100vh',
        padding: '20px',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'flex-start',
        alignItems: 'stretch',
        position: 'absolute',
        left: -15,
        top: -22,
        overflowY: 'auto',
    };

    const mainContentStyle = {
        marginLeft: '160px',
        marginTop: '0px',
        width: 'calc(100vw - 160px)',
        height: '100vh',
        overflow: 'hidden',
    };

    const inputStyle = {
        padding: '5px',
        margin: '5px 0',
        borderRadius: '5px',
        border: '1px solid #ccc',
    };

    const lineStyleContainerStyle = {
        display: 'flex',
        flexDirection: 'column',
        marginBottom: '10px',
    };

    const lineStyleInputsStyle = {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
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
                        {view === 'graph' && (
                            <>
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
                                        max="100"
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Search for a person"
                                        value={query}
                                        onChange={(e) => setQuery(e.target.value)}
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                    <select 
                                        onChange={resultSelect}
                                        value={highlightedNode || ''}
                                        style={{...inputStyle, width: '80%'}}
                                    >
                                        <option value="">Select a person</option>
                                        {formatOptions(searchResults).map(node => (
                                            <option key={node.id} value={node.id}>
                                                {node.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <button style={buttonStyle} onClick={() => setShowStatistics(true)}>Show Statistics</button>
                                <div>
                                    <button style={buttonStyle} onClick={handleZoomIn}>Zoom In</button>
                                    <button style={buttonStyle} onClick={handleZoomOut}>Zoom Out</button>
                                    <button style={buttonStyle} onClick={handleCenterView}>Center View</button>
                                </div>
                            </>
                        )}
                        {view === 'tree' && (
                            <>
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
                                        max="100"
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Search for a person"
                                        value={query}
                                        onChange={(e) => setQuery(e.target.value)}
                                        style={{...inputStyle, width: '80%'}}
                                    />                                    
                                </div>
                                <div>
                                    <h4>Line Styles:</h4>
                                    <h5>Colour, Width, Dash level</h5>
                                    {['Parent-Child', 'Current Spouse', 'Divorced Spouse'].map((type, index) => {
                                        const key = Object.keys(lineStyles)[index];
                                        const style = lineStyles[key];
                                        return (
                                            <div key={key} style={lineStyleContainerStyle}>
                                                <label>{type}</label>
                                                <div style={lineStyleInputsStyle}>
                                                    <input
                                                        type="color"
                                                        value={style.color}
                                                        onChange={handleLineStyleChange(key, 'color')}
                                                        style={{ ...inputStyle, width: '30px', padding: '0' }}
                                                    />
                                                    <input
                                                        type="number"
                                                        value={style.width}
                                                        onChange={handleLineStyleChange(key, 'width')}
                                                        min="1"
                                                        max="10"
                                                        style={{ ...inputStyle, width: '40px' }}
                                                    />
                                                    <input
                                                        type="text"
                                                        value={style.dashArray}
                                                        onChange={handleLineStyleChange(key, 'dashArray')}
                                                        placeholder="e.g., 5,5"
                                                        style={{ ...inputStyle, width: '70px' }}
                                                    />
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </>
                        )}
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
                                setZoomIn={setZoomIn}
                                setZoomOut={setZoomOut}
                                setCenterView={setCenterView}
                            /> : 
                            <FamilyTree 
                                generations={generations} 
                                query={query}
                                lineStyles={lineStyles}
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