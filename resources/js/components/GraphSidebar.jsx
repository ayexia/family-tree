import React, { useState } from 'react';
import axios from 'axios';

const GraphSidebar = ({ node, onClose, setImages, images }) => {
  const [errorMessage, setErrorMessage] = useState('');

  if (!node || !node.data) return null;

  const uploadImage = async (event) => {
    const selectedFile = event.target.files[0];
    if (!selectedFile) return;

    const formData = new FormData();
    formData.append('image', selectedFile);
    formData.append('id', node.id);

    try {
      const response = await axios.post('/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      setImages((prevImages) => ({ ...prevImages, [node.id]: response.data.imagePath }));
    } catch (error) {
      setErrorMessage('Image could not be uploaded.');
    }
  };

  return (
    <div style={{
      width: '300px',
      position: 'fixed',
      right: 0,
      top: 0,
      height: '100%',
      backgroundColor: '#92B08E',
      boxShadow: '-2px 0 5px rgba(0,0,0,0.5)',
      overflowY: 'auto',
      transition: 'transform .3s',
      transform: 'translate(0px)',
    }}>
      <button onClick={onClose} style={{
        backgroundColor: '#37672F',
        color: 'white',
        border: 'none',
        padding: '10px',
        cursor: 'pointer',
        position: 'absolute',
        top: '10px',
        right: '10px',
        borderRadius: '5px',
        zIndex: 1,
      }}>
        &times;
      </button>
      <div style={{ padding: '20px' }}>
        <h3>{node.data.name || 'Unknown'}</h3>
        <p><img src={images[node.id] ||node.data.image || '/images/user.png'} height={250} width={250} /></p>
        <div style={{ marginTop: '10px' }}>
          <label htmlFor="upload-button" style={{
            backgroundColor: '#37672F',
            color: 'white',
            padding: '10px',
            borderRadius: '5px',
            cursor: 'pointer',
            display: 'inline-block'
          }}>
            Upload Image
          </label>
          <input id="upload-button" type="file" onChange={uploadImage} style={{ display: 'none' }} />
        </div>
        <p>DOB: {node.data.birth_date || 'Unknown date'}</p>
        <p>DOD: {node.data.death_date || 'Unknown date'}</p>
        {node.data.marriages && node.data.marriages.length > 0 ? (
          node.data.marriages.map((marriage, index) => (
            <div key={index}>
              <p>Marriage {index + 1}: {marriage.marriage_date || 'Unknown date'}</p>
              <p>Divorce {index + 1}: {marriage.divorce_date || 'Unknown date'}</p>
            </div>
          ))
        ) : (
          <p>No marriages</p>
        )}
        <p>Parents: {node.data.parents && node.data.parents.length > 0 ? 
          node.data.parents.map(parent => parent.name).join(', ') 
          : 'Unknown person'}
        </p>
        {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
      </div>
    </div>
  );
};

export default GraphSidebar;