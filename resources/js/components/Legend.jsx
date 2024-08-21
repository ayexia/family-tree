import React from 'react';

const Legend = () => {
  const legendStyles = {
    display: 'flex',
    flexDirection: 'row',
    alignItems: 'flex-start',
    padding: '10px',
    borderRadius: '10px',
    color: '#9BB08C',
    fontFamily: 'Inika, serif',
    fontSize: '15px',
    fontWeight: 'bold',
    position: 'absolute',
    top: '0px', 
    left: '175px', 
    zIndex: '10',
    width: '900px',
  };

  const legendItemStyle = {
    display: 'flex',
    alignItems: 'center',
    marginBottom: '0px',
  };

  const colourBoxStyle = (colour) => ({
    width: '20px',
    height: '20px',
    backgroundColor: colour,
    marginRight: '15px',
    marginLeft: '15px',
    borderRadius: '50%',
  });

  const lineStyle = (dash) => ({
    width: '40px',
    height: '2px',
    marginRight: '10px',
    marginLeft: '15px',
    backgroundColor: dash ? 'transparent' : 'red',
    border: dash ? '1px dashed blue' : 'none',
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
        <div style={colourBoxStyle('yellow')}></div>
        <span>Highlighted</span>
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