import React, { useState } from 'react';

const Sidebar = ({ node, onClose, setImages, images }) => {
const [errorMessage, setErrorMessage] = useState('');

  if (!node) return null;

  const uploadImage = async (event) => { //function for uploading images
    const selectedFile = event.target.files[0]; //sets the selectedFile as the first file the user selects
    if (!selectedFile) return; //if no file has been selected do nothing
  
    const formData = new FormData(); //new formdata object is created (used for build the data needed to pass to the server)
    formData.append('image', selectedFile); //adds the selected file as a value for key "image" for formdata
    formData.append('id', node.id); //adds the selected node's id as a value for key "id" for formdata (used to associate the image with the correct node)
  
    try { //sends request to upload-image endpoint with formdata as the data
      const response = await axios.post('/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data', //necessary for file uploads
        },
      });
      setImages((prevImages) => ({ ...prevImages, [node.id]: response.data.imagePath })) //updates state with new image and sets it to node
    } catch (error) {
      setErrorMessage('Image could not be uploaded.'); //error message if image cannot be uploaded for any reason
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
        <h3>{node.name || 'Unknown'}</h3>
        <p><img src={images[node.id] || node.attributes.image ||'/images/user.png'} height={250} width={250}></img></p>
        <div style={{ marginTop: '10px'}}>
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
        <p>DOB: {node.attributes.DOB}</p>
        <p>DOD: {node.attributes.DOD}</p>
        {node.attributes.marriages.map((marriage, index) => (
          <div key={index}>
            <p>Marriage {index + 1}: {marriage.marriage_date}</p>
            <p>Divorce {index + 1}: {marriage.divorce_date}</p>
          </div>
        ))}
         <p>Parents:</p>
        {node.attributes.parents && node.attributes.parents.length > 0 ? (
          <ul>
            {node.attributes.parents.map((parent, index) => (
              <li key={index}>{parent.name || 'Unknown person'}</li>
            ))}
          </ul>
        ) : (
          <p>Unknown parents</p>
        )}
        {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
      </div>
    </div>
  );
};

export default Sidebar;