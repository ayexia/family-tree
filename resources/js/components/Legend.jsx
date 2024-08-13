import React from 'react';

const Legend = () => {
  const legendStyles = {
    display: 'flex',
    flexDirection: 'column',
    alignItems: 'flex-start',
    padding: '10px',
    backgroundColor: '#9BB08C',
    borderRadius: '10px',
    color: '#EDECD7',
    fontFamily: 'Inika, serif',
    fontSize: '18px',
    position: 'absolute',
    top: '50px', 
    left: '20px', 
    zIndex: '5',
    maxWidth: '200px',
    overflow: 'auto',
  };

  const legendItemStyle = {
    display: 'flex',
    alignItems: 'center',
    marginBottom: '10px',
  };

  const colourBoxStyle = (colour) => ({
    width: '20px',
    height: '20px',
    backgroundColor: colour,
    marginRight: '10px',
    borderRadius: '50%',
  });

  const lineStyle = (dash) => ({
    width: '40px',
    height: '2px',
    backgroundColor: dash ? 'transparent' : 'red',
    border: dash ? '1px dashed blue' : 'none',
    marginRight: '10px',
  });

  return (
    <div style={legendStyles}>
      <div style={legendItemStyle}>
        <div style={colourBoxStyle('#97EBE6')}></div>
        <span>Male</span>
      </div>
      <div style={legendItemStyle}>
        <div style={colourBoxStyle('#EB97CF')}></div>
        <span>Female</span>
      </div>
      <div style={legendItemStyle}>
        <div style={colourBoxStyle('#EBC097')}></div>
        <span>Other/Unknown</span>
      </div>
      <div style={legendItemStyle}>
        <div style={lineStyle(false)}></div>
        <span>Current Spouse</span>
      </div>
      <div style={legendItemStyle}>
        <div style={lineStyle(true)}></div>
        <span>Former Spouse</span>
      </div>
    </div>
  );
};

export default Legend;