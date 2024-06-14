import React from 'react';
import ReactDOM from 'react-dom';
import Recharts from './components/recharts';

const App = () => (
  <div>
    <Recharts/> 
  </div>
);

const root = ReactDOM.createRoot(document.getElementById('rechart_root'));
root.render(<App />);