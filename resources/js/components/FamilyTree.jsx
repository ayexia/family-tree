import React, { useState, useEffect } from 'react'; //react modules handling states and effects of components
import Tree from 'react-d3-tree'; //Uses react-d3-tree package for visual representatin of tree structure
import axios from 'axios'; //used to fetch api data through making http requests

const FamilyTree = () => {
  const [treeData, setTreeData] = useState(null); //initialises variable treeData

  useEffect(() => {
    fetchFamilyTreeData(); //after component is mounted calls this function which retrieves the family tree data from the api through http requests
  }, []);

  const fetchFamilyTreeData = async () => {
    try {
      const response = await axios.get('/api/family-tree-json'); //uses axios library to make http request to fetch api data, which is then parsed as JSON
      setTreeData(response.data); //the fetched data is stored in treeData variable
    } catch (error) {
      console.error('Error fetching family tree data:', error); //if any issues with retrieving data will print error message
    }
  };

  if (!treeData) {
    return <div>Loading...</div>; //alternate display if no tree data is available
  }

  return ( //utilises react-d3-tree library to set parameters for tree display
    //sets width and height of display, the data to be used, the orientation of the tree and style of links/branches, positioning of tree and spacing between sibling and non-sibling nodes
    <div style={{ width: '100%', height: '100vh' }}>
      <Tree
        data={treeData}
        orientation="vertical"
        pathFunc="step"
        translate={{ x: 300, y: 50 }}
        separation={{ siblings: 2, nonSiblings: 2 }}
      />
    </div>
  );
};

export default FamilyTree; //exports component for use