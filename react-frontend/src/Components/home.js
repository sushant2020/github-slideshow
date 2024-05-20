import React, {Component} from "react";
import {Image, Input, Space,Tag,Divider,Table,Card,Row,Col,Dropdown,Menu,AutoComplete} from "antd";
import { Link, BrowserRouter as Router, withRouter, Switch , Redirect} from 'react-router-dom';
import MediaQuery from 'react-responsive';
import App from "../App";
import Appm from "../mApp"
import axios from 'axios';
import SearchField from "react-search-field";
// import { withApollo } from "react-apollo";
const { Search } = Input;

const data = [
    {
      key: 'PO105',
      name: '1ST CALL SERVICES',
      qty: '32',
      price: '1599',
      note: '-',
      status: 'Pending',
      tags: ['All'],
    },
    {
      key: 'PO106',
      name: '365HEALTHCARE',
      qty: '42',
      price: '1999',
      note: '-',
      status: 'Pending',
      tags: ['Manage manual Import'],
    },

  ];

  const data1 = [
    {
      value: 'ACAT31',
      type: 'Tablet',
      desc: 'Acamprosate 333mg gastro-resistant tablets',
      price: '38.25',
      date: '09-06-2021',
    },
    {
      value: 'ACAT19',
      type: 'Tablet',
      desc: 'Acarbose 100mg tablets',
      price: '25.29',
      date: '09-06-2021',
    },
    {
      value: 'ACAT59',
      type: 'Tablet',
      desc: 'Acarbose 50mg tablets',
      price: '14.58',
      date: '09-06-2021',
      },
    {
      value: 'ACE16',
      type: 'capsule',
      desc: 'Acetylcysteine 600mg capsules',
      price: '47.59',
      date: '09-06-2021',
    },
  ];

  const newsFeed = [
      {value:"ACAT16  had been added to short-dated."},
      {value:"ACAT31  has been added in offer."}
  ]

  const options = [
    {
      value: 'ACE16',
    },
    // {
    //   value: 'Downing Street',
    // },
    // {
    //   value: 'Wall Street',
    // },
  ];

class Home extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
        collapsed: false,
        menu: false,
        prodData: [],
        tableData:[],
        };
      }

    componentDidMount(){
        // console.log("In CDM");

        localStorage.removeItem('productName');

        let getURL= 'https://api.sigmaproductmaster.webdezign.uk/api/products'
        // const resp = axios.get(`${Api.getProducts}`);
        axios.get(getURL).then((response) => {
          // console.log("Respons...::",response.data)

          this.setState({
            prodData: response.data
          })
          // setPost(response.data);
        });
        let userData = localStorage.getItem('usenDetails');
        // console.log("USER>>>",userData);
        if(userData == null ){
          // this.props.history.push('/')
        }
        // console.log("In CDM:::",resp);
    }

    onSearch=(n)=>{
      // console.log("In Search...",n.length)
      if(n.length > 2){
       let arr = [];
       this.state.prodData.map((i, j) => {
          //console.log("....",i)
         if (
          //  i.dt_description.toLowerCase().includes(n.toLowerCase()) 
        //  || i.key.includes(n) 
        //  || i.phone.toLowerCase().includes(n.toLowerCase())
        i.parent_product_code.toLowerCase().includes(n.toLowerCase())
          // || i.address.toLowerCase().includes(n.toLowerCase())
          ) {
           // console.log("....",i)
            let obj={
              value : i.parent_product_code 
            }
            arr.push(obj);
         }
         if (
          i.clean_description.toLowerCase().includes(n.toLowerCase()) 
       //  || i.key.includes(n) 
       //  || i.phone.toLowerCase().includes(n.toLowerCase())
        //  || i.sm_analysis_code4.toLowerCase().includes(n.toLowerCase())
         // || i.address.toLowerCase().includes(n.toLowerCase())
         ) {
           let obj={
             value : i.clean_description
           }
           arr.push(obj);
          
        }
       });

      //  data.map((p)=>{
      //   p.tags.map((q)=>{{
      //    // console.log("...Tags",q)
      //     if (q.toLowerCase().includes(n.toLowerCase())) {
      //      arr.push(p);
      //    }
      //   }}) 
      // })
       
       // console.log("Search Index:: ",index)
      // console.log("Search Name UM:: ",arr)
       this.setState({
         tableData: arr
       })
      }
       if(n == ''){
        // console.log("Search No Result:: ")
         
         this.setState({
           tableData: []
         })
       
      }
     }

     onSelected=(option)=>{
      //  console.log("In Selected..",option)

       this.props.history.push('/products')
     }

     onGoto=()=>{
      this.props.history.push('/createPO')
     }

    render(){

      const {tableData} = this.state;

        const columns = [
            {
              title: 'PO-ID',
              // ...this.getColumnSearchProps('key'),
              render:(data)=>{
                return(
                  <h3 style={{fontWeight:'bold',fontSize: "15px"}}>{data.key}</h3>
                )
              }
              //dataIndex: 'key',
            },
           
            {
                title: 'Quantity',
                // dataIndex: 'name',
                key: 'qty',
                // defaultSortOrder: 'descend',
                sorter: (a, b) => a.qty - b.qty,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.qty}</h3>
                  )
                }
              },
              {
                title: 'Price',
                // dataIndex: 'name',
                key: 'price',
                sorter: (a, b) => a.qty - b.qty,
                render:(data)=>{
                  // console.log("Data..",data)
                  return(
                    <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.price}</h3>
                  )
                }
              },
          ];
          // console.log("In table data..",this.state.tableData)
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
              <div>
        <App header={

          // <SearchField style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius: 5}}
          //   placeholder="Search..."
          //   onChange={(e)=>this.onSearch(e)}
          //   // searchText="This is initial search text"
          //   // classNames="test-class"
          // />
          <AutoComplete style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius: 5}}
            options={tableData.length > 0 ? tableData : []}
            placeholder="Search Prod Code / DT Description"
            // filterOption={(inputValue, option) => option.value.toUpperCase().indexOf(inputValue.toUpperCase()) !== -1
          // }
            // onChange={(e)=>this.onSearch(e)}
            // onSelect={(option)=>this.onSelected(option)}
            // onSearch={(val)=>this.onSearch(val)}     
          />
          
    }
    
        >
          {/ {console.log(":::",tableData)} /}
          
             <img alt="example" src="https://images.unsplash.com/photo-1587370560942-ad2a04eabb6d?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTZ8fHBoYXJtYWN5fGVufDB8fDB8fA%3D%3D&ixlib=rb-1.2.1&w=1000&q=80" 
              style={{width:"90%",height:"90%",position: "absolute",zIndex:"-100px"}}/>
             
              <div style={{alignSelf:"center",marginLeft:"32%",marginTop:"14%"}}>
              
            </div>
               <div style={{marginTop:"-10%",display: "flex"}}>
              
              <div style={{float: "left",width: "40%",position:"absolute",zIndex:'100',height:"400px",marginLeft:"43%"}}>
              <p 
             style={{fontSize:"22px",position:"absolute",zIndex:'100',marginLeft:"29%",marginTop:"1%",fontWeight:"bold",color:'#213A87'}}>
             Pending Purchase Orders
             </p>
             <div style={{marginTop:"7%"}}>
             
             {data.map((p)=>{
                 return(
             <Card hoverable onClick={()=> {this.onGoto()}} style={{marginTop:"1%",width:"80%",borderRadius:10,marginLeft:"6%" }}>
                <Row justify="space-around">
                <Col span={4}>PO-Id</Col>
                <Col span={4}>Qty</Col>
                <Col span={4}>Price</Col>
                </Row> 
                <Row justify="space-around">
                <Col span={4} style={{fontWeight:"bold"}}>{p.key}</Col>
                <Col span={4} style={{fontWeight:"bold"}}>{p.qty}</Col>
                <Col span={4} style={{fontWeight:"bold"}}>{p.price}</Col>
                </Row>    
             </Card>
                 )
            })
             }
             </div>
             
              </div>

             <div style={{float: "left",width: "40%",position:"absolute",zIndex:'100',marginLeft:"2%"}}>
             <p 
             style={{fontSize:"22px",position:"absolute",zIndex:'100',marginLeft:"37%",marginTop:"1%",fontWeight:"bold",color:'#213A87'}}>
              News Feeds
             </p>
             <div style={{marginTop:"7%"}}>
             {newsFeed.map((p)=>{
                 return(
             <Card style={{marginTop:"1%",width:"80%",borderRadius:10,marginLeft:"6%" }}>
                <Row justify="space-around">
      <Col span={22} style={{fontWeight:"bold"}}>{p.value}</Col>
    </Row>    
             </Card>
                 )
            })
             }
             </div>

             </div>
            
            </div>
               
         </App>
         </div>
          )}
          else{
            return(
              <div style={{minHeight: 700,background:"#E8E8E8"}}>
                <Appm>

              <div>
             
             <div style={{textAlign:"center"}}>
              <p style={{fontSize:"19px",marginTop:"1%",fontWeight:"bold",color:'#213A87'}}>
             Pending Purchase Orders
             </p>
             </div>

             <div style={{marginTop:"2%"}}>
             
             {data.map((p)=>{
                 return(
             <Card hoverable onClick={()=> {this.onGoto()}} style={{margin:"2%",width:"96%",borderRadius:10,}}>
                <Row justify="space-around">
                <Col span={4}>PO-Id</Col>
                <Col span={4}>Qty</Col>
                <Col span={4}>Price</Col>
                </Row> 
                <Row justify="space-around">
                <Col span={4} style={{fontWeight:"bold"}}>{p.key}</Col>
                <Col span={4} style={{fontWeight:"bold"}}>{p.qty}</Col>
                <Col span={4} style={{fontWeight:"bold"}}>{p.price}</Col>
                </Row>    
             </Card>
                 )
            })
             }
             </div>
             
              </div>

              
                <div style={{marginTop:"5%"}}>
              <div style={{textAlign:"center"}}> 
             <p 
             style={{fontSize:"19px",marginTop:"1%",fontWeight:"bold",color:'#213A87'}}>
              News Feeds
             </p>
             </div>
             {/* <div style={{}}> 
             <p 
             style={{fontSize:"18px",position:"absolute",zIndex:'100',marginTop:"1%",fontWeight:"bold",color:'#213A87'}}>
              News Feeds
             </p>
             </div> */}
             {newsFeed.map((p)=>{
                 return(
             <Card style={{marginTop:"1%",width:"96%",borderRadius:10,margin:"2%"}}>
                <Row justify="space-around">
      <Col span={22} style={{fontWeight:"bold"}}>{p.value}</Col>
    </Row>    
             </Card>
                 )
            })
             }
             </div>

              </Appm>
              </div>
            )
          }
      }
      }
      </MediaQuery>
     )   
    }

}

export default Home