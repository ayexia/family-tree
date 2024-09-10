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

  const viewProfile = () => {
    window.location.href = `/member/profile/${node.id}`;
  };

  const isBirthday = (birthDate) => {
    if (!birthDate) return false;
    const today = new Date();
    const [year, month, day] = birthDate.split('-').map(Number);
  
    const birthdayThisYear = new Date(today.getFullYear(), month - 1, day);
  
    return today.getMonth() === birthdayThisYear.getMonth() && today.getDate() === birthdayThisYear.getDate();
  };

  const isTodayBirthday = isBirthday(node.attributes.DOB);
  
  const buttonStyle = {
    backgroundColor: '#004d40',
    color: '#edecd7',
    padding: '8px 4px',
    borderRadius: '5px',
    cursor: 'pointer',
    border: 'none',
    display: 'inline-block',
    fontFamily: 'Inika, serif',
    fontSize: '0.8em',
    margin: '0 5px 5px 0',
    whiteSpace: 'nowrap',
    flex: '1 1 auto',
    textAlign: 'center',
  };
  
  return (
    <div style={{
      width: '300px',
      position: 'fixed',
      right: 0,
      top: 0,
      height: '100%',
      backgroundColor: '#008080',
      boxShadow: '-2px 0 5px rgba(0,0,0,0.5)',
      overflowY: 'auto',
      transition: 'transform .3s',
      transform: 'translate(0px)',
      zIndex: '500',
    }}>
      <button onClick={onClose} style={{
        backgroundColor: '#004d40',
        color: '#edecd7',
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
            <span>It's {node.name}'s birthday today!</span>
          </div>
        )}
        <h3 style={{ color: '#edecd7' }}>{node.name || 'Unknown'}</h3>
        <p><img src={images[node.id] || node.attributes.image ||'/images/user.png'} height={250} width={250} /></p>
        <div style={{ display: 'flex', flexWrap: 'nowrap', marginTop: '10px', justifyContent: 'space-between' }}>
          <label htmlFor="upload-button" style={buttonStyle}>
            Upload Image
          </label>
          <input id="upload-button" type="file" onChange={uploadImage} style={{ display: 'none' }} />

          <button onClick={edit} style={buttonStyle}>
            Edit Details
          </button>

          <button onClick={viewProfile} style={buttonStyle}>
            View Profile
          </button>
        </div>

        <p style={{ color: '#edecd7' }}>Date of birth: {node.attributes.DOB || "Unknown date"}</p>
        <p style={{ color: '#edecd7' }}>Birthplace: {node.attributes.birth_place || "Unknown"}</p>
        <p style={{ color: '#edecd7' }}>Date of death: {node.attributes.DOD || "Unknown date"}</p>
        <p style={{ color: '#edecd7' }}>Resting place: {node.attributes.death_place || "Unknown"}</p>
        {node.attributes.marriage_dates && node.attributes.marriage_dates.length > 0 ? (
          node.attributes.marriage_dates.map((marriage, index) => (
            <div key={index}>
              <p style={{ color: '#edecd7' }}>Marriage {index + 1}: {marriage || 'Unknown date'}</p>
              {node.attributes.divorce_dates && node.attributes.divorce_dates[index] && (
                <p style={{ color: '#edecd7' }}>Divorce {index + 1}: {node.attributes.divorce_dates[index]}</p>
              )}
            </div>
          ))
        ) : (
          <p style={{ color: '#edecd7' }}>No marriages</p>
        )}
         <div>
         <p style={{ color: '#edecd7' }}>Parents:</p>
          {node.attributes.parents && Object.keys(node.attributes.parents).length > 0 ? (
            <ul>
              {Object.values(node.attributes.parents).map(parent => {
                let parentType = parent.gender === 'F' ? 'Mother' : parent.gender === 'M' ? 'Father' : 'Parent';
                return (
                  <li key={parent.id} style={{ color: '#edecd7' }}>
                    {parentType}: {parent.name || 'Unknown person'}
                  </li>
                );
              })}
            </ul>
          ) : (
            <ul>
            <li style={{ color: '#edecd7' }}>Mother: Unknown person</li>
            <li style={{ color: '#edecd7' }}>Father: Unknown person</li>
            </ul>
          )}
        </div>
          <div>
          <p style={{ color: '#edecd7' }}>Pets:</p>            
        {node.attributes.pets && node.attributes.pets.length > 0 ? (
            <ul>
              {node.attributes.pets.map((pet, index) => (
                <li key={index} style={{ color: '#edecd7' }}>{pet}</li>
              ))}
            </ul>
        ) : (
          <p style={{ color: '#edecd7' }}>No pets</p>
        )}        
        </div>
          <div>
          <p style={{ color: '#edecd7' }}>Hobbies:</p>            
        {node.attributes.hobbies && node.attributes.hobbies.length > 0 ? (
            <ul>
              {node.attributes.hobbies.map((hobby, index) => (
                <li key={index} style={{ color: '#edecd7' }}>{hobby}</li>
              ))}
            </ul>
        ) : (
          <p style={{ color: '#edecd7' }}>No hobbies</p>
        )}        
        </div>
          <div>
          <p style={{ color: '#edecd7' }}>Notes:</p>
        {node.attributes.notes ? (
            <p style={{ color: '#edecd7' }}>{node.attributes.notes}</p>
        ) : (
          <p style={{ color: '#edecd7' }}>No notes</p>
        )}        
        </div>
        {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
      </div>
    </div>
  );
};

export default Sidebar;