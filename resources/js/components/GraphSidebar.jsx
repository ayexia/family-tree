//imports required react and utility packages
import React, { useState } from 'react';
import axios from 'axios';
//imports birthday icon component
import { Cake } from 'lucide-react';
//imports tooltip functionality
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css'; 

//sidebar component for graph view displaying member details
const GraphSidebar = ({ node, onClose, setImages, images }) => {
  //stores any error messages during image upload
  const [errorMessage, setErrorMessage] = useState('');

  //returns nothing if no node is selected or node has no data
  if (!node || !node.data) return null;

  //handles image upload functionality
  const uploadImage = async (event) => {
    const selectedFile = event.target.files[0];
    if (!selectedFile) return;

    const formData = new FormData();
    formData.append('image', selectedFile);
    formData.append('id', node.id);

    try {
      //sends image to server
      const response = await axios.post('/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      //updates image state with new upload
      setImages((prevImages) => ({ ...prevImages, [node.id]: response.data.imagePath }));
    } catch (error) {
      setErrorMessage('Image could not be uploaded.');
    }
  };

  //handles navigation to edit page
  const handleEdit = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    window.location.href = `/person/${node.id}/edit`;
  };

  //handles navigation to profile view
  const handleViewProfile = (e) => {
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }
    window.location.href = `/member/profile/${node.id}`;
  };

  //checks if today matches person's birthday
  const isBirthday = (birthDate) => {
    if (!birthDate) return false;
    const today = new Date();
    const [year, month, day] = birthDate.split('-').map(Number);
  
    const birthdayThisYear = new Date(today.getFullYear(), month - 1, day);
  
    return today.getMonth() === birthdayThisYear.getMonth() && today.getDate() === birthdayThisYear.getDate();
  };

  //checks if today is selected person's birthday
  const isTodayBirthday = isBirthday(node.data.birth_date);

  //defines common button styling
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

  //returns sidebar component structure
 return (
  //main sidebar container with styling
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
        //displays birthday banner if it's person's birthday
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
      <h3 style={{ color: '#edecd7' }}>{node.data.name || 'Unknown'}</h3>
      <p><img src={images[node.id] || node.data.image || '/images/user.png'} height={250} width={250} /></p>  
      <div style={{ display: 'flex', flexWrap: 'nowrap', marginTop: '10px', justifyContent: 'space-between' }}>
      <Tippy content="Upload a new image">
        <label htmlFor="upload-button" style={buttonStyle}>
          Upload Image
        </label>
      </Tippy>
      <input 
        id="upload-button" 
        type="file" 
        onChange={uploadImage} 
        style={{ display: 'none' }} 
      />

      <Tippy content="Edit details for this person">
        <button 
          type="button"
          onClick={handleEdit}
          style={buttonStyle}
        >
          Edit Details
        </button>
        </Tippy>

      <Tippy content="View the full profile of this person">
        <button 
          type="button"
          onClick={handleViewProfile}
          style={buttonStyle}
        >
          View Profile
        </button>
      </Tippy>
    </div>

      <p style={{ color: '#edecd7' }}>Date of birth: {node.data.birth_date || 'Unknown date'}</p>
      <p style={{ color: '#edecd7' }}>Birthplace: {node.data.birth_place || 'Unknown'}</p>
      <p style={{ color: '#edecd7' }}>Date of death: {node.data.death_date || 'Unknown date'}</p>
      <p style={{ color: '#edecd7' }}>Resting place: {node.data.death_place || 'Unknown'}</p>

      {node.data.marriage_dates && node.data.marriage_dates.length > 0 ? (
        //maps through each marriage date
        node.data.marriage_dates.map((marriage, index) => (
          <div key={index}>
            <p style={{ color: '#edecd7' }}>Marriage {index + 1}: {marriage || 'Unknown date'}</p>
            {node.data.divorce_dates && node.data.divorce_dates[index] && (
              <p style={{ color: '#edecd7' }}>Divorce {index + 1}: {node.data.divorce_dates[index]}</p>
            )}
          </div>
        ))
      ) : (
        //displays if no marriages recorded
        <p style={{ color: '#edecd7' }}>No marriages</p>
      )}
        
       <div>
         <p style={{ color: '#edecd7' }}>Parents:</p>
         {node.data.parents && Object.keys(node.data.parents).length > 0 ? (
           //displays list of known parents
           <ul>
             {Object.values(node.data.parents).map(parent => {
               //determines parent type based on gender
               let parentType = parent.gender === 'F' ? 'Mother' : parent.gender === 'M' ? 'Father' : 'Parent';
               return (
                 <li key={parent.id} style={{ color: '#edecd7' }}>
                   {parentType}: {parent.name || 'Unknown person'}
                 </li>
               );
             })}
           </ul>
         ) : (
           //displays if no parents recorded
           <ul>
             <li style={{ color: '#edecd7' }}>Mother: Unknown person</li>
             <li style={{ color: '#edecd7' }}>Father: Unknown person</li>
           </ul>
         )}
       </div>
         <div>
           <p style={{ color: '#edecd7' }}>Pets:</p>            
           {node.data.pets && node.data.pets.length > 0 ? (
             //displays list of pets if any exist
             <ul>
               {node.data.pets.map((pet, index) => (
                 <li key={index} style={{ color: '#edecd7' }}>{pet}</li>
               ))}
             </ul>
           ) : (
             //displays if no pets recorded
             <p style={{ color: '#edecd7' }}>No pets</p>
           )}        
         </div>
         <div>
           <p style={{ color: '#edecd7' }}>Hobbies:</p>            
           {node.data.hobbies && node.data.hobbies.length > 0 ? (
             //displays list of hobbies if any exist
             <ul>
               {node.data.hobbies.map((hobby, index) => (
                 <li key={index} style={{ color: '#edecd7' }}>{hobby}</li>
               ))}
             </ul>
           ) : (
             //displays if no hobbies recorded
             <p style={{ color: '#edecd7' }}>No hobbies</p>
           )}        
         </div>
         <div>
           <p style={{ color: '#edecd7' }}>Notes:</p>
           {node.data.notes ? (
             //displays notes if any exist
             <p style={{ color: '#edecd7' }}>{node.data.notes}</p>
           ) : (
             //displays if no notes recorded
             <p style={{ color: '#edecd7' }}>No notes</p>
           )}        
         </div>
       {errorMessage && <p style={{ color: 'red' }}>{errorMessage}</p>}
     </div>
   </div>
 );
};

//exports graph sidebar component for use in application
export default GraphSidebar;