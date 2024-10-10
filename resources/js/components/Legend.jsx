import React from 'react';

const Legend = ({ lineStyles }) => {
  const legendStyles = {
    display: 'flex',
    flexDirection: 'row',
    flexWrap: 'wrap',
    alignItems: 'flex-start',
    padding: '10px',
    borderRadius: '10px',
    color: '#004d40',
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

  const colourBoxStyle = (colour, isHexagon = false) => ({
    width: '10px',
    height: '10px',
    backgroundColor: isHexagon ? 'transparent' : colour,
    marginRight: '5px',
    borderRadius: isHexagon ? '0' : '50%',
    clipPath: isHexagon ? 'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)' : 'none',
    border: isHexagon ? '2px solid black' : 'none',
  });

  const lineStyle = (style) => ({
    width: '40px',
    height: '0px',
    marginRight: '5px',
    borderBottom: `${style.width}px ${style.dashArray === 'none' ? 'solid' : 'dashed'} ${style.color}`,
  });

  return (
    <div style={legendStyles}>
      <div style={legendItemStyle}>
        <div style={colourBoxStyle(lineStyles?.nodeMale?.color || '#97EBE6')}></div>
        <span>Male</span>
      </div>
      <div style={legendItemStyle}>
        <div style={colourBoxStyle(lineStyles?.nodeFemale?.color || '#EB97CF')}></div>
        <span>Female</span>
      </div>
      <div style={legendItemStyle}>
        <div style={colourBoxStyle(lineStyles?.nodeOther?.color || '#EBC097')}></div>
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
      <div style={legendItemStyle}>
        <div style={colourBoxStyle('transparent', true)}></div>
        <span>Adopted</span>
      </div>
      {lineStyles && (
        <>
          {lineStyles.parentChild && (
            <div style={legendItemStyle}>
              <div style={lineStyle(lineStyles.parentChild)}></div>
              <span>Parent-Child</span>
            </div>
          )}
          {lineStyles.current && (
            <div style={legendItemStyle}>
              <div style={lineStyle(lineStyles.current)}></div>
              <span>Current Spouse</span>
            </div>
          )}
          {lineStyles.divorced && (
            <div style={legendItemStyle}>
              <div style={lineStyle(lineStyles.divorced)}></div>
              <span>Divorced</span>
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default Legend;