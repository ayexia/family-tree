import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import { ReactFlowProvider } from 'reactflow';
import FamilyTree from './FamilyTree';
import FamilyGraph from './FamilyGraph';
import FamilyTreePDF from './FamilyTreePDF';

const App = () => {
    const [view, setView] = useState('tree');
    const [showPDF, setShowPDF] = useState(false);

    const switchView = () => {
        setView(view === 'tree' ? 'graph' : 'tree');
    };

    const exportToPDF = () => {
        setShowPDF(true);
    };

    const buttonStyle = {
        backgroundColor: '#CCE7BD',
        color: '#A7B492',
        padding: '10px 20px',
        border: 'none',
        borderRadius: '50px',
        cursor: 'pointer',
        fontSize: '1em',
        fontFamily: '"Inika", serif',
        fontWeight: 'bold',
        transition: 'background-color 0.3s',
        margin: '10px',
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
    };

    const imgStyle = {
        width: '24px',
        height: '24px',
        opacity: 0.3,
    };

    const buttonContainer = {
        position: 'absolute',
        top: '0px',
        right: '20px',
        display: 'flex',
        flexDirection: 'column',
    };

    return (
        <ReactFlowProvider>
            <Router>
                <div>
                    <div style={buttonContainer}>
                        <button style={buttonStyle} onClick={switchView}>
                            <img src="/images/grid.png" alt="Grid" style={imgStyle} />
                            Switch to {view === 'tree' ? 'Graph View' : 'Tree View'}
                        </button>
                        <button style={buttonStyle} onClick={exportToPDF}>
                            <img src="/images/printing.png" alt="PDF" style={imgStyle} />
                            Export to PDF
                        </button>
                    </div>

                    <Routes>
                        <Route path="/family-tree" element={<FamilyTree />} />
                        <Route path="/family-graph" element={<FamilyGraph />} />
                    </Routes>
                    {view === 'tree' ? (
                        <FamilyTree />
                    ) : (
                        <FamilyGraph />
                    )}
                    {showPDF && <FamilyTreePDF onClose={() => setShowPDF(false)} />}
                </div>
            </Router>
        </ReactFlowProvider>
    );
};

export default App;