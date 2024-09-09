import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import { ReactFlowProvider } from 'reactflow';
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import FamilyTree from './FamilyTree';
import FamilyGraph from './FamilyGraph';
import FamilyTreePDF from './FamilyTreePDF';

const DEFAULT_LINE_STYLES = {
    parentChild: { color: '#000000', width: 2, dashArray: 'none' },
    current: { color: '#FF0000', width: 2, dashArray: 'none' },
    divorced: { color: '#808080', width: 2, dashArray: '5,5' },
    adopted: { color: '#FFA500', width: 2, dashArray: '10,5' },
    nodeMale: { color: '#97EBE6' },
    nodeFemale: { color: '#EB97CF' },
    nodeOther: { color: '#EBC097' }
};

const App = () => {
    const [view, setView] = useState('graph');
    const [showPDF, setShowPDF] = useState(false);
    const [generations, setGenerations] = useState(3);
    const [desiredName, setDesiredName] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [highlightedNode, setHighlightedNode] = useState(null);
    const [showStatistics, setShowStatistics] = useState(false);
    const [zoomIn, setZoomIn] = useState(() => {});
    const [zoomOut, setZoomOut] = useState(() => {});
    const [centerView, setCenterView] = useState(() => {});
    const [lineStyles, setLineStyles] = useState(() => {
        const savedStyles = localStorage.getItem('lineStyles');
        return savedStyles ? { ...DEFAULT_LINE_STYLES, ...JSON.parse(savedStyles) } : DEFAULT_LINE_STYLES;
    });

    useEffect(() => {
        localStorage.setItem('lineStyles', JSON.stringify(lineStyles));
    }, [lineStyles]);

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
        padding: '2px 6px',
        border: 'none',
        borderRadius: '15px',
        cursor: 'pointer',
        fontSize: '0.8em',
        fontFamily: '"Inika", serif',
        fontWeight: 'bold',
        transition: 'background-color 0.3s',
        margin: '3px 0',
        display: 'flex',
        alignItems: 'center',
        gap: '5px',
        width: '90%',
        height: '24px',
    };

    const topButtonStyle = {
        ...buttonStyle,
        fontSize: '0.8em',
        padding: '3px 6px',
        marginBottom: '1px',
        height: '35px',
    };

    const imgStyle = {
        width: '18px',
        height: '18px',
        opacity: 0.3,
    };

    const controlsContainer = {
        width: '180px',
        height: '85vh',
        padding: '35px 8px 8px 8px',
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
        marginLeft: '180px',
        marginTop: '0px',
        width: 'calc(100vw - 180px)',
        height: '77vh',
        overflow: 'hidden',
    };

    const inputStyle = {
        padding: '2px',
        margin: '3px 0',
        borderRadius: '3px',
        border: '1px solid #CCE7BD',
        fontSize: '0.7em',
        fontFamily: '"Inika", serif',
        width: '90%'
    };

    const lineStyleContainer = {
        display: 'flex',
        flexDirection: 'column',
        marginBottom: '1px',
    };

    const lineStyleInputs = {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        width: '90%',
    };

    const currentViewStyle = {
        fontFamily: '"Inika", serif',
        fontSize: '0.9em',
        textAlign: 'center',
        position: 'absolute',
        color: '#587353',
        top: '-25px',
        right: '550px',
        marginBottom: '0px'
    };

    const zoomControlsStyle = {
        display: 'flex',
        justifyContent: 'space-between',
        marginTop: '1px',
    };

    const zoomButtonStyle = {
        ...buttonStyle,
        padding: '2px',
        width: '30%',
    };

    const genderIconColoursStyle = {
        display: 'flex',
        justifyContent: 'space-between',
        marginTop: '1px',
    };

    const genderIconColourInputStyle = {
        ...inputStyle,
        width: '30%',
        padding: '0',
    };

    const TippyButton = ({ onClick, content, children, isTopButton = false }) => (
        <Tippy content={content} placement="right" arrow={true}>
            <button style={isTopButton ? topButtonStyle : buttonStyle} onClick={onClick}>
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
                            isTopButton={true}
                        >
                            <img src="/images/grid.png" alt="Grid" style={imgStyle} />
                            Switch to {view === 'graph' ? 'Tree View' : 'Graph View'}
                        </TippyButton>                        
                        <TippyButton
                            onClick={exportToPDF}
                            content="Export your family tree to a PDF book"
                            isTopButton={true}
                        >
                            <img src="/images/printing.png" alt="PDF" style={imgStyle} />
                            Export PDF
                        </TippyButton>
                        <div style={{ marginTop: '2px' }}>
                            <label htmlFor="generations" style={{fontSize: '0.7em'}}>Generations: </label>
                            <Tippy content="Specify the number of generations to display">
                            <input
                                type="number"
                                id="generations"
                                value={generations}
                                onChange={(e) => setGenerations(Number(e.target.value))}
                                min="1"
                                max="100"
                                style={{...inputStyle, width: '40px'}}
                            />
                            </Tippy>
                        </div>
                        <Tippy content="Search for a person in the family tree">
                        <input
                            type="text"
                            placeholder="Search person"
                            value={desiredName}
                            onChange={(e) => setDesiredName(e.target.value)}
                            style={{...inputStyle, width: '95%', marginTop: '1px'}}
                        />
                        </Tippy>
                        {view === 'graph' && (
                            <>
                                <Tippy content="Select a person from the search results">
                                <select 
                                    onChange={resultSelect}
                                    value={highlightedNode || ''}
                                    style={{...inputStyle, width: '100%', marginTop: '1px'}}
                                >
                                    <option value="">Select a person</option>
                                    {formatOptions(searchResults).map(node => (
                                        <option key={node.id} value={node.id}>
                                            {node.name}
                                        </option>
                                    ))}
                                </select>
                                </Tippy>
                                <TippyButton
                                onClick={() => setShowStatistics(true)}
                                content="Show statistics related to the family tree"
                                >
                                <img src="/images/statistics.png" alt="Statistics" style={imgStyle} />
                                Show Statistics
                                </TippyButton>
                                <div style={zoomControlsStyle}>
                                    <Tippy content="Zoom in to the view">
                                        <button onClick={handleZoomIn} style={zoomButtonStyle}>
                                            <img src="/images/zoom-in.png" alt="Zoom In" style={imgStyle} />
                                        </button>
                                    </Tippy>
                                    <Tippy content="Zoom out of the view">
                                        <button onClick={handleZoomOut} style={zoomButtonStyle}>
                                            <img src="/images/zoom-out.png" alt="Zoom Out" style={imgStyle} />
                                        </button>
                                    </Tippy>
                                    <Tippy content="Center the view on the current focus">
                                        <button onClick={handleCenterView} style={zoomButtonStyle}>
                                            <img src="/images/center-view.png" alt="Center View" style={imgStyle} />
                                        </button>
                                    </Tippy>
                                </div>
                            </>
                        )}
                        <div style={{ marginTop: '1px' }}>
                        <h4 style={{fontSize: '0.75em', marginBottom: '1px'}}>Line Styles:</h4>
                        <h5 style={{fontSize: '0.7em', marginTop: '1px', marginBottom: '1px'}}>Colour, Width, Dash level</h5>
                        {['parentChild', 'current', 'divorced'].map((key) => {
                            const type = {
                                parentChild: 'Parent-Child',
                                current: 'Current Spouse',
                                divorced: 'Divorced Spouse',
                            }[key];
                            const style = lineStyles[key];
                            return (
                                <div key={key} style={{...lineStyleContainer, marginBottom: '1px'}}>
                                    <label style={{fontSize: '0.65em'}}>{type}</label>
                                    <div style={lineStyleInputs}>
                                        <Tippy content={`Set the colour for ${type} line`}>
                                            <input
                                                type="color"
                                                value={style.color}
                                                onChange={handleLineStyleChange(key, 'color')}
                                                style={{ ...inputStyle, width: '20%', padding: '0' }}
                                            />
                                        </Tippy>
                                        <Tippy content={`Set the width for ${type} line`}>
                                            <input
                                                type="number"
                                                value={style.width}
                                                onChange={handleLineStyleChange(key, 'width')}
                                                min="1"
                                                max="10"
                                                style={{ ...inputStyle, width: '30%' }}
                                            />
                                        </Tippy>
                                        <Tippy content={`Set the dash pattern for ${type} line (e.g., 5,5 for dashed line)`}>
                                            <input
                                                type="text"
                                                value={style.dashArray}
                                                onChange={handleLineStyleChange(key, 'dashArray')}
                                                placeholder="e.g., 5,5"
                                                style={{ ...inputStyle, width: '40%' }}
                                            />
                                        </Tippy>
                                    </div>
                                </div>
                            );
                        })}
                        {view === 'graph' && (
                            <div style={{...lineStyleContainer, marginBottom: '1px'}}>
                                <label style={{fontSize: '0.65em'}}>Adopted</label>
                                <div style={lineStyleInputs}>
                                    <Tippy content="Set the colour for Adopted line">
                                        <input
                                            type="color"
                                            value={lineStyles.adopted.color}
                                            onChange={handleLineStyleChange('adopted', 'color')}
                                            style={{ ...inputStyle, width: '20%', padding: '0' }}
                                        />
                                    </Tippy>
                                    <Tippy content="Set the width for Adopted line">
                                        <input
                                            type="number"
                                            value={lineStyles.adopted.width}
                                            onChange={handleLineStyleChange('adopted', 'width')}
                                            min="1"
                                            max="10"
                                            style={{ ...inputStyle, width: '30%' }}
                                        />
                                    </Tippy>
                                    <Tippy content="Set the dash pattern for Adopted line (e.g., 10,5 for dashed line)">
                                        <input
                                            type="text"
                                            value={lineStyles.adopted.dashArray}
                                            onChange={handleLineStyleChange('adopted', 'dashArray')}
                                            placeholder="e.g., 10,5"
                                            style={{ ...inputStyle, width: '40%' }}
                                        />
                                    </Tippy>
                                </div>
                            </div>
                        )}
                    </div>
                    <div style={{ marginTop: '1px' }}>
                    <h4 style={{fontSize: '0.75em', marginBottom: '1px'}}>Gender Icon Colours:</h4>
                    <h5 style={{fontSize: '0.7em', marginTop: '1px', marginBottom: '1px'}}>Male, Female, Other</h5>
                        <div style={genderIconColoursStyle}>
                            {['Male', 'Female', 'Other'].map((type) => (
                                <Tippy key={type} content={`Set the colour for ${type} gender icons`}>
                                    <input
                                        type="color"
                                        value={lineStyles[`node${type}`].color}
                                        onChange={handleLineStyleChange(`node${type}`, 'color')}
                                        style={genderIconColourInputStyle}
                                    />
                                </Tippy>
                            ))}
                        </div>
                    </div>
                </div>
                <div style={mainContentStyle}>
                    {view === 'graph' ? 
                        <FamilyGraph 
                            generations={generations} 
                            desiredName={desiredName} 
                            showStatistics={showStatistics}
                            setShowStatistics={setShowStatistics}
                            highlightedNode={highlightedNode}
                            setSearchResults={setSearchResults}
                            setHighlightedNode={setHighlightedNode}
                            setZoomIn={setZoomIn}
                            setZoomOut={setZoomOut}
                            setCenterView={setCenterView}
                            lineStyles={lineStyles}
                        /> : 
                        <FamilyTree 
                            generations={generations} 
                            desiredName={desiredName}
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