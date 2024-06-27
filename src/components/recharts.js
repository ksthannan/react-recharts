import React, { useEffect, useState } from "react";
import {
  AreaChart,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Area,
} from "recharts";
import { __ } from "@wordpress/i18n";
import "./../assets/style.scss";

const DataDisplay = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [period, setPeriod] = useState("7");
  const [fullData, setFullData] = useState([]);

  useEffect(() => {
    const data_url = rechartsObj.root + "recharts/v1/data";
    const now = new Date();
    const sevenDaysAgo = new Date(now.setDate(now.getDate() - period));

    const fetchData = async () => {
      try {
        // condition for preventing to reduce api request and load only first time
        if (!fullData.length) {
          const response = await fetch(data_url, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": rechartsObj.nonce,
            },
          });

          if (!response.ok) {
            throw new Error(__("Network response was not ok", "recharts"));
          }
          const result = await response.json();
          const filteredData = result.filter((item) => {
            const itemDate = new Date(item.created_at);
            return itemDate >= sevenDaysAgo;
          });
          setData(filteredData);

          // this will prevent to reduce api request
          setFullData(result);
        } else {
          const filteredData = fullData.filter((item) => {
            const itemDate = new Date(item.created_at);
            return itemDate >= sevenDaysAgo;
          });
          setData(filteredData);
        }
      } catch (error) {
        setError(error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [period]);

  const handlePeriodChange = (event) => {
    setPeriod(event.target.value);
  };

  if (loading) {
    return <div>{__("Loading...", "recharts")} </div>;
  }

  if (error) {
    return (
      <div>
        {__("Error:", "recharts")} {error.message}
      </div>
    );
  }

  return (
    <div>
      <div className="graph-period">
        <div className="widget-tite">
          <h2>{__("Graph Widget", "recharts")}</h2>
        </div>
        <div className="widget-elect">
          <select
            id="period"
            name="period"
            value={period}
            onChange={handlePeriodChange}
          >
            <option value="7">{__("Last 7 Days", "recharts")}</option>
            <option value="15">{__("Last 15 Days", "recharts")}</option>
            <option value="30">{__("Last 30 Days", "recharts")}</option>
          </select>
        </div>
      </div>
      {!data.length ? (
        <div className="simple-info">
          <p>{__("No data available within the date range!", "recharts")}</p>
        </div>
      ) : (
        <>
          <AreaChart
            width={500}
            height={250}
            data={data}
            margin={{ top: 10, right: 30, left: 0, bottom: 0 }}
          >
            <defs>
              <linearGradient id="colorUv" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#8884d8" stopOpacity={0.8} />
                <stop offset="95%" stopColor="#8884d8" stopOpacity={0} />
              </linearGradient>
              <linearGradient id="colorPv" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="#82ca9d" stopOpacity={0.8} />
                <stop offset="95%" stopColor="#82ca9d" stopOpacity={0} />
              </linearGradient>
            </defs>
            <XAxis dataKey="name" />
            <YAxis />
            <CartesianGrid strokeDasharray="3 3" />
            <Tooltip />
            <Area
              type="monotone"
              dataKey="uv"
              stroke="#8884d8"
              fillOpacity={1}
              fill="url(#colorUv)"
            />
            <Area
              type="monotone"
              dataKey="pv"
              stroke="#82ca9d"
              fillOpacity={1}
              fill="url(#colorPv)"
            />
          </AreaChart>
        </>
      )}
    </div>
  );
};

export default DataDisplay;
