import React, { useState } from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import FamilyTree from './FamilyTree';
import FamilyGraph from './FamilyGraph';

const App = () => {
    const [view, setView] = useState('tree');

    const switchView = () => {
        setView(view === 'tree' ? 'graph' : 'tree');
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
        position: 'absolute',
        top: '0px',
        right: '20px',
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
    };

    const imgStyle = {
        width: '24px',
        height: '24px',
        opacity: 0.3,
    };

    return (
        <Router>
            <div>
                <button style={buttonStyle} onClick={switchView}>
                    <img src="/images/grid.png" alt="Grid" style={imgStyle} />
                    Switch to {view === 'tree' ? 'Graph View' : 'Tree View'}
                </button>

                <Routes>
                    <Route path="/family-tree" element={<FamilyTree />} />
                    <Route path="/family-graph" element={<FamilyGraph />} />
                </Routes>

                {view === 'tree' ? <FamilyTree /> : <FamilyGraph />}
            </div>
        </Router>
    );
};

export default App;