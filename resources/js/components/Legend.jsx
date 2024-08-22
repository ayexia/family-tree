import React from 'react';

const Legend = ({ lineStyles }) => {
  const legendStyles = {
    display: 'flex',
    flexDirection: 'row',
    flexWrap: 'wrap',
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
    width: '1200px',
  };

  const legendItemStyle = {
    display: 'flex',
    alignItems: 'center',
    marginBottom: '5px',
    marginRight: '15px',
  };

  const colourBoxStyle = (colour) => ({
    width: '20px',
    height: '20px',
    backgroundColor: colour,
    marginRight: '5px',
    borderRadius: '50%',
  });

  const lineStyle = (style) => ({
    width: '40px',
    height: `${style.width}px`,
    marginRight: '5px',
    backgroundColor: style.color,
    border: 'none',
    strokeDasharray: style.dashArray,
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
        <div style={colourBoxStyle('#FFD700')}></div>
        <span>Birthday</span>
      </div>
      {lineStyles && Object.entries(lineStyles).map(([key, style]) => (
        <div key={key} style={legendItemStyle}>
          <div style={lineStyle(style)}></div>
          <span>{key === 'parentChild' ? 'Parent-Child' : key === 'current' ? 'Current Spouse' : 'Divorced Spouse'}</span>
        </div>
      ))}
    </div>
  );
};

export default Legend;