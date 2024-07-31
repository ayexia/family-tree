import React from 'react';
import { BrowserRouter as Router, Route, Routes, Link } from 'react-router-dom';
import FamilyTree from './FamilyTree';
import FamilyGraph from './FamilyGraph';

const App = () => {
    return (
        <Router>
            <div>
                <nav>
                    <ul>
                        <li>
                            <Link to="/family-tree">Family Tree</Link>
                        </li>
                        <li>
                            <Link to="/family-graph">Family Graph</Link>
                        </li>
                    </ul>
                </nav>

                <Routes>
                    <Route path="/family-tree" element={<FamilyTree />} />
                    <Route path="/family-graph" element={<FamilyGraph />} />
                    <Route path="*" element={<h2>Page Not Found</h2>} />
                </Routes>
            </div>
        </Router>
    );
};

export default App;