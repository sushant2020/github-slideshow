import React, {Component} from "react";
import {Form, Button, Row, Col, Input, Select, Modal, Divider, Table, Tag, Popconfirm ,Space,message } from "antd";
import App from "../App";
import Appm from "../mApp"
import MediaQuery from 'react-responsive';
import axios from 'axios';
import { List, Flex, WhiteSpace } from 'antd-mobile';
// import { Chart } from 'react-charts'
// import { Doughnut, Bar, Line } from 'react-chartjs-2';
// import BarChart from 'react-bar-chart'
// import Chart from 'react-apexcharts'
// import { withApollo } from "react-apollo";
// import ReactApexChart from 'apexcharts'
import Chart from 'react-apexcharts'

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    EditFilled,
    HighlightFilled,
    DeleteFilled
  
  } from '@ant-design/icons';

  const { Option } = Select;
  const { Search } = Input;

  const barData = [
    {text: 'Jan', value: 500}, 
    {text: 'Feb', value: 400},
    {text: 'Mar', value: 300}, 
    {text: 'Apr', value: 200},
    {text: 'May', value: 100}, 
    {text: 'June', value: 600} 
  ];
   
  const margin = {top: 20, right: 20, bottom: 30, left: 40};
 
class Reports extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           
           options: {
            chart: {
              id: 'apexchart-example',
              type: 'bar',
              height: 350,
              stacked: true,
              toolbar: {
                show: true
              },
              zoom: {
                enabled: true
              }
            },
            responsive: [{
              breakpoint: 480,
              options: {
                legend: {
                  position: 'bottom',
                  offsetX: -10,
                  offsetY: 0
                }
              }
            }],
            plotOptions: {
              bar: {
                horizontal: false,
                borderRadius: 10
              },
            },
            legend: {
              position: 'right',
              offsetY: 40
            },
            //   xaxis: {
            //   type: 'datetime',
            //   categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT',
            //     '01/05/2011 GMT', '01/06/2011 GMT'
            //   ],
            // },
            xaxis: {
              categories: ['Jan','Feb','Mar','Apr','May','June']
            }
          },
          
          // series: [{
          //   name: 'series-1',
          //   data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
          // }],

          series: [{
            name: 'PRODUCT ACE16',
            data: [44, 55, 41, 67, 22, 43]
          }, 
          
          // {
          //   name: 'PRODUCT B',
          //   data: [13, 23, 20, 8, 13, 27]
          // }, {
          //   name: 'PRODUCT C',
          //   data: [11, 17, 15, 15, 21, 14]
          // }, {
          //   name: 'PRODUCT D',
          //   data: [21, 7, 25, 13, 22, 8]
          // }
        ],

        series1: [{
          name: 'ACE16',
          data: [10, 12, 11, 17, 12, 20]
        },  
        {
          name: 'ACAT19',
          data: [13, 23, 20, 8, 13, 27]
        }, {
          name: 'ACAT59',
          data: [11, 17, 15, 15, 21, 14]
        }, {
          name: 'ACET66',
          data: [21, 7, 25, 13, 22, 8]
        }
      ],
          options1: {
            chart: {
              type: 'line',
              height: 350,
              stacked: true,
              toolbar: {
                show: true
              },
              zoom: {
                enabled: true
              }
            },
            responsive: [{
              breakpoint: 480,
              options: {
                legend: {
                  position: 'bottom',
                  offsetX: -10,
                  offsetY: 0
                }
              }
            }],
            plotOptions: {
              bar: {
                horizontal: false,
                borderRadius: 10
              },
            },
            xaxis: {
              type: 'datetime',
              categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT',
                '01/05/2011 GMT', '01/06/2011 GMT'
              ],
            },
            legend: {
              position: 'right',
              offsetY: 40
            },
            fill: {
              opacity: 1
            }
          },
          
        };
      }

    componentDidMount(){
        // console.log("In CDM");
        // let getURL= 'https://sigmaproductmaster.mywebdezign.uk/api/products'
        // // const resp = axios.get(`${Api.getProducts}`);
        // axios.get(getURL).then((response) => {
        //   console.log("Response...::",response.data)

        //   // this.setState({
        //   //   prodData: response.data,
        //   //   prodloading: false
        //   // })
        // })
    }

    handleBarClick=(element, id)=>{ 
      console.log(`The bin ${element.text} with id ${id} was clicked`);
    }
    
    render(){

      const data2 = [
        
          {
            label: 'Supplier 1',
            data: [[0, 1], [1, 2], [2, 4], [3, 2], [4, 7]]
          },
          {
            label: 'Supplier 2',
            data: [[0, 3], [1, 1], [2, 5], [3, 6], [4, 4]]
          },
          {
            label: 'Supplier 3',
            data: [[0, 10], [2, 3.5], [3, 5], [4, 6], [4, 5]]
          },
          {
            label: 'Supplier 4',
            data: [[0, 2], [1, 3], [2, 7], [3, 9], [4, 10]]
          },
         
        ]

      const axes = [
          { primary: true, type: 'linear', position: 'bottom' },
          { type: 'linear', position: 'left' }
      ]
    
    const { data5, randomizeData } = ({
      series: 10,
      dataType: 'ordinal'
    })

      
      
      // var chart = new ApexCharts(document.querySelector('#chart'), this.state.options)
      // chart.render()
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App 
    //   header={
    //     <Input placeholder="Search " size="large"
    //     allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
    //     // onChange={(val)=>this.setState({searchVal: val})}
    //     onSearch={(val)=>this.onSearch(val)}
    //     />
    //   // <Search placeholder="Search in Tag" size="large"
    //   // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} onSearch={(val)=>this.onSearch(val)}/>
    //   }
      >
        
    <Chart options={this.state.options1} series={this.state.series1} type="line" width={1500} height={720} />
    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Products Price Variation)</h4>
      </div>

    <Chart options={this.state.options} series={this.state.series} type="bar" width={1500} height={720} />

    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Product Sold in Qty Month Wise)</h4>
      </div>

      <Chart options={this.state.options1} series={this.state.series1} type="bar" width={1500} height={720} />
    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Products Price Variation)</h4>
      </div>

      {/* <Chart options={this.state.options1} series={this.state.series1} type="radialBar" width={1500} height={720} />
    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Products Price Variation)</h4>
      </div> */}

      </App>
      )
    }else{
      return(
      <Appm>

<h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Reports</h4> 

        <Chart options={this.state.options1} series={this.state.series1} type="line" width={"370px"} height={"200px"} />
    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Products Price Variation)</h4>
      </div>

    <Chart options={this.state.options} series={this.state.series} type="bar" width={"370px"} height={"200px"}  />

    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Product Sold in Qty Month Wise)</h4>
      </div>

      <Chart options={this.state.options1} series={this.state.series1} type="bar" width={"370px"} height={"200px"} />
    <div style={{textAlign:"center",marginBottom:"5%"}}>
      <h4>(Products Price Variation)</h4>
      </div>
      </Appm>
      )
    }
  }
}
</MediaQuery>
     )   
    }

}

export default Reports