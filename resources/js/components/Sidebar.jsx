import React, { useState } from 'react';
import axios from 'axios'; 
import { Cake } from 'lucide-react';

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

  const edit = () => {
    window.location.href = `/person/${node.id}/edit`;
  };

  const isBirthday = (birthDate) => {
    if (!birthDate) return false;
    const today = new Date();
    const birth = new Date(birthDate);
    return today.getMonth() === birth.getMonth() && today.getDate() === birth.getDate();
  };

  const isTodayBirthday = isBirthday(node.attributes.DOB);
  
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
      zIndex: '500',
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
        {isTodayBirthday && (
          <div style={{ 
            backgroundColor: '#FFD700', 
            padding: '10px', 
            borderRadius: '5px', 
            marginBottom: '10px',
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
          }}>
            <Cake size={24} />
            <span>{node.name} was born today!</span>
          </div>
        )}
        <h3>{node.name || 'Unknown'}</h3>
        <p><img src={images[node.id] || node.attributes.image ||'/images/user.png'} height={250} width={250}></img></p>
        <div style={{ display: 'flex', gap: '10px', marginTop: '10px' }}>
          <div>
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

          <button onClick={edit} style={{
            backgroundColor: '#37672F',
            color: 'white',
            padding: '10px',
            borderRadius: '5px',
            cursor: 'pointer',
            border: 'none',
            display: 'inline-block',
            fontFamily: 'Inika, serif',
            fontSize: '1em',
          }}>
            Edit Details
          </button>
        </div>

        <p>Date of birth: {node.attributes.DOB || "Unknown date"}</p>
        <p>Birthplace: {node.attributes.birth_place || "Unknown"}</p>
        <p>Date of death: {node.attributes.DOD || "Unknown date"}</p>
        <p>Resting place: {node.attributes.death_place || "Unknown"}</p>
        {node.attributes.marriage_dates && node.attributes.marriage_dates.length > 0 ? (
          node.attributes.marriage_dates.map((marriage, index) => (
            <div key={index}>
              <p>Marriage {index + 1}: {marriage || 'Unknown date'}</p>
              {node.attributes.divorce_dates && node.attributes.divorce_dates[index] && (
                <p>Divorce {index + 1}: {node.attributes.divorce_dates[index]}</p>
              )}
            </div>
          ))
        ) : (
          <p>No marriages</p>
        )}
         <div>
          <p>Parents:</p>
          {node.attributes.parents && Object.keys(node.attributes.parents).length > 0 ? (
            <ul>
              {Object.values(node.attributes.parents).map(parent => {
                let parentType = parent.gender === 'F' ? 'Mother' : parent.gender === 'M' ? 'Father' : 'Parent';
                return (
                  <li key={parent.id}>
                    {parentType}: {parent.name || 'Unknown person'}
                  </li>
                );
              })}
            </ul>
          ) : (
            <ul>
            <li>Mother: Unknown person</li>
            <li>Father: Unknown person</li>
            </ul>
          )}
        </div>
        {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
      </div>
    </div>
  );
};

export default Sidebar;