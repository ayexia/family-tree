import './bootstrap';
import Alpine from 'alpinejs';
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './components/App';

window.Alpine = Alpine;

Alpine.start();

const rootElement = document.getElementById('root');
const root = ReactDOM.createRoot(rootElement);

root.render(<App />);