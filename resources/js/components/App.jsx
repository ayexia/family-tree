import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import { ReactFlowProvider } from 'reactflow';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
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
        height: '85vh',
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
        height: '77vh',
        overflow: 'hidden',
    };

    const inputStyle = {
        padding: '5px',
        margin: '2.5px 0',
        borderRadius: '5px',
        border: '1px solid #ccc',
        fontSize: '0.85em',
        fontFamily: '"Inika", serif',
        border: '1px solid #CCE7BD',
    };

    const lineStyleContainer = {
        display: 'flex',
        flexDirection: 'column',
        marginBottom: '10px',
    };

    const lineStyleInputs = {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
    };

    const currentViewStyle = {
        fontFamily: '"Inika", serif',
        fontSize: '1em',
        textAlign: 'center',
        position: 'absolute',
        color: '#587353',
        top: '-27.5px',
        right: '550px',
        marginBottom: '0px'
    };

    const TippyButton = ({ onClick, content, children }) => (
        <Tippy content={content} placement="right" arrow={true}>
            <button style={buttonStyle} onClick={onClick}>
                {children}
            </button>
        </Tippy>
    );

    return (
        <ReactFlowProvider>
            <Router>
            <div style={{ display: 'flex', flexDirection: 'column' }}>
                <p style={currentViewStyle}>Current View - {view === 'graph' ? 'Graph' : 'Tree'}</p>
                <div style={{ display: 'flex' }}>
                    <div style={controlsContainer}>
                        <TippyButton
                            onClick={switchView}
                            content="Switch between Tree and Graph view"
                        >
                            <img src="/images/grid.png" alt="Grid" style={imgStyle} />
                            Switch to {view === 'graph' ? 'Tree View' : 'Graph View'}
                        </TippyButton>                        
                        {view === 'graph' && (
                            <>
                                <TippyButton
                                    onClick={exportToPDF}
                                    content="Export your family tree to a PDF book"
                                >
                                    <img src="/images/printing.png" alt="PDF" style={imgStyle} />
                                    Export to PDF book
                                </TippyButton>
                                <div>
                                    <label htmlFor="generations">Generations: </label>
                                    <Tippy content="Specify the number of generations to display">
                                    <input
                                        type="number"
                                        id="generations"
                                        value={generations}
                                        onChange={(e) => setGenerations(Number(e.target.value))}
                                        min="1"
                                        max="100"
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                    </Tippy>
                                </div>
                                <div>
                                    <Tippy content="Search for a person in the family tree">
                                    <input
                                        type="text"
                                        placeholder="Search for a person"
                                        value={query}
                                        onChange={(e) => setQuery(e.target.value)}
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                    </Tippy>
                                    <Tippy content="Select a person from the search results">
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
                                    </Tippy>
                                </div>
                                <TippyButton
                                onClick={() => setShowStatistics(true)}
                                content="Show statistics related to the family tree"
                            >
                                Show Statistics
                                </TippyButton>
                                <div>
                                <TippyButton onClick={handleZoomIn} content="Zoom in to the view">Zoom In</TippyButton>
                                <TippyButton onClick={handleZoomOut} content="Zoom out of the view">Zoom Out</TippyButton>
                                <TippyButton onClick={handleCenterView} content="Center the view on the current focus">Center View</TippyButton>
                                </div>
                            </>
                        )}
                        {view === 'tree' && (
                            <>
                                <TippyButton
                                    onClick={exportToPDF}
                                    content="Export your family tree to a PDF book"
                                >
                                    <img src="/images/printing.png" alt="PDF" style={imgStyle} />
                                    Export to PDF book
                                </TippyButton>
                                <div>
                                    <label htmlFor="generations">Generations: </label>
                                    <Tippy content="Specify the number of generations to display">
                                    <input
                                        type="number"
                                        id="generations"
                                        value={generations}
                                        onChange={(e) => setGenerations(Number(e.target.value))}
                                        min="1"
                                        max="100"
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                    </Tippy>
                                </div>
                                <div>
                                    <Tippy content="Search for a person in the family tree">
                                    <input
                                        type="text"
                                        placeholder="Search for a person"
                                        value={query}
                                        onChange={(e) => setQuery(e.target.value)}
                                        style={{...inputStyle, width: '80%'}}
                                    />
                                    </Tippy>                                    
                                </div>
                                <div>
                                    <h4>Line Styles:</h4>
                                    <h5>Colour, Width, Dash level</h5>
                                    {['Parent-Child', 'Current Spouse', 'Divorced Spouse'].map((type, index) => {
                                        const key = Object.keys(lineStyles)[index];
                                        const style = lineStyles[key];
                                        return (
                                            <div key={key} style={lineStyleContainer}>
                                                <label>{type}</label>
                                                <div style={lineStyleInputs}>
                                                    <Tippy content={`Set the colour for ${type} line`}>
                                                    <input
                                                        type="color"
                                                        value={style.color}
                                                        onChange={handleLineStyleChange(key, 'color')}
                                                        style={{ ...inputStyle, width: '30px', padding: '0' }}
                                                    />
                                                    </Tippy>
                                                    <Tippy content={`Set the width for ${type} line`}>
                                                    <input
                                                        type="number"
                                                        value={style.width}
                                                        onChange={handleLineStyleChange(key, 'width')}
                                                        min="1"
                                                        max="10"
                                                        style={{ ...inputStyle, width: '40px' }}
                                                    />
                                                    </Tippy>
                                                    <Tippy content={`Set the dash pattern for ${type} line (e.g., 5,5 for dashed line)`}>
                                                    <input
                                                        type="text"
                                                        value={style.dashArray}
                                                        onChange={handleLineStyleChange(key, 'dashArray')}
                                                        placeholder="e.g., 5,5"
                                                        style={{ ...inputStyle, width: '70px' }}
                                                    />
                                                    </Tippy>
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