import React, { useState } from 'react';
import axios from 'axios';
import { Cake } from 'lucide-react';

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

  const edit = () => {
    window.location.href = `/person/${node.id}/edit`;
  };

  const isBirthday = (birthDate) => {
    if (!birthDate) return false;
    const today = new Date();
    const birth = new Date(birthDate);
    return today.getMonth() === birth.getMonth() && today.getDate() === birth.getDate();
  };

  const isTodayBirthday = isBirthday(node.data.birth_date);

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
            <span>It's {node.data.name}'s birthday today!</span>
          </div>
        )}
        <h3>{node.data.name || 'Unknown'}</h3>
        <p><img src={images[node.id] || node.data.image || '/images/user.png'} height={250} width={250} /></p>  
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

        <p>Date of birth: {node.data.birth_date || 'Unknown date'}</p>
        <p>Birthplace: {node.data.birth_place || 'Unknown'}</p>
        <p>Date of death: {node.data.death_date || 'Unknown date'}</p>
        <p>Resting place: {node.data.death_place || 'Unknown'}</p>
        {node.data.marriage_dates && node.data.marriage_dates.length > 0 ? (
          node.data.marriage_dates.map((marriage, index) => (
            <div key={index}>
              <p>Marriage {index + 1}: {marriage || 'Unknown date'}</p>
              {node.data.divorce_dates && node.data.divorce_dates[index] && (
                <p>Divorce {index + 1}: {node.data.divorce_dates[index]}</p>
              )}
            </div>
          ))
        ) : (
          <p>No marriages</p>
        )}
        
        <div>
          <p>Parents:</p>
          {node.data.parents && Object.keys(node.data.parents).length > 0 ? (
            <ul>
              {Object.values(node.data.parents).map(parent => {
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
          <div>
            <p>Pets:</p>            
            {node.data.pets && node.data.pets.length > 0 ? (
              <ul>
                {node.data.pets.map((pet, index) => (
                  <li key={index}>{pet}</li>
                ))}
              </ul>
            ) : (
              <p>No pets</p>
            )}        
          </div>
          <div>
            <p>Hobbies:</p>            
            {node.data.hobbies && node.data.hobbies.length > 0 ? (
              <ul>
                {node.data.hobbies.map((hobby, index) => (
                  <li key={index}>{hobby}</li>
                ))}
              </ul>
            ) : (
              <p>No hobbies</p>
            )}        
          </div>
          <div>
            <p>Notes:</p>
            {node.data.notes ? (
              <p>{node.data.notes}</p>
            ) : (
              <p>No notes</p>
            )}        
          </div>
        {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
      </div>
    </div>
  );
};

export default GraphSidebar;