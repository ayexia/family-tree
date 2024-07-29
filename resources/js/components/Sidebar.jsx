import React from 'react';

const Sidebar = ({ node, onClose }) => {
  if (!node) return null;

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
        <h3>{node.name}</h3>
        <p><img src={node.attributes.image ||'/images/user.png'} height={250} width={250}></img></p>
        <p>DOB: {node.attributes.DOB}</p>
        <p>DOD: {node.attributes.DOD}</p>
        {node.attributes.marriages.map((marriage, index) => (
          <div key={index}>
            <p>Marriage {index + 1}: {marriage.marriage_date}</p>
            <p>Divorce {index + 1}: {marriage.divorce_date}</p>
          </div>
        ))}
        Parents: {node.attributes.parents.map(parent => parent.name).join(', ') || 'Unknown person'}<br />
      </div>
    </div>
  );
};

export default Sidebar;