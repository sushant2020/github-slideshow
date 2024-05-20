import React, {Component} from "react";
import {Button, Card, Row, Col, Tooltip, Modal, Form, Select, Input, Table, Space} from "antd";
import App from "../App";
import Appm from "../mApp"
import MediaQuery from 'react-responsive';
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
// import { withApollo } from "react-apollo";
import CreatePurchase from "./createPurchase"
import {
    AppstoreOutlined,
    PlusOutlined,
    MinusCircleOutlined,
    EyeOutlined,
    EditOutlined,
    StarTwoTone,
    HeartTwoTone,
  } from '@ant-design/icons';
// import Form from "antd/lib/form/Form";

const { Option } = Select;
const {form} = Form
const { Search } = Input;

const prodsData = [
  {
    key: 'ACE16',
    type: 'Tablet',
    desc: 'ACECLOFENAC 100MG TABLETS',
    price: '4.59',
    forecast: '',
    comment: '',
    qty:'60',
    date: '01-05-2021',
    todate: '01-07-2021',
  },
  {
    key: 'ACET66',
    type: 'capsule',
    desc: 'Acetylcysteine 600mg capsules',
    price: '47.59',
    forecast: '',
    comment: '',
    qty:'50',
    date: '01-01-2021',
    todate: '01-05-2021',
  },
  {
    key: 'ACAT31',
    type: 'Tablet',
    desc: 'Acamprosate 333mg gastro-resistant tablets',
    price: '38.25',
    forecast: '',
    comment: '',
    qty:'45',
    date: '09-02-2021',
    todate: '09-05-2021',
  },
  {
    key: 'ACAT19',
    type: 'Tablet',
    desc: 'Acarbose 100mg tablets',
    price: '25.29',
    forecast: '',
    comment: '',
    qty:'33',
    date: '19-03-2021',
    todate: '19-05-2021',
  },
  {
    key: 'ACAT59',
    type: 'Tablet',
    desc: 'Acarbose 50mg tablets',
    price: '14.58',
    forecast: '',
    comment: '',
    qty:'30',
    date: '24-04-2021',
    todate: '24-05-2021',
    },
  

];

const priceData = [
  {
    key: 'Tier 1 - Supplier',
    name: 'SPOT',
    age: 32,
    phone: '9988776655',
    address: 'New York No. 1 Lake Park',
    code: '5.4',
    email: 'john.brown@gmail.com',
    status: 'Active',
    tags: ['developer'],
  },
  {
    key: 'Tier 0 - DT Price Type',
    name: 'MORNIN',
    age: 42,
    phone: '9988776644',
    address: 'London No. 1 Lake Park',
    code: '5.5',
    email: 'jim.green@gmail.com',
    status: 'Active',
    tags: ['Administrator'],
  },
  {
    key: 'Tier 2 - IRP',
    name: 'RIVOPHA',
    age: 32,
    phone: '9988776633',
    address: 'Sydney No. 1 Lake Park',
    code: '2',
    email: 'joe.black@gmail.com',
    status: 'Active',
    tags: ['Manager'],
  },
  
];

const relatedProd = [
  {
    key: '1',
    name: 'ALLPHA',
    age: 32,
    phone: 'Acarbose 50mg tablets',
    address: '1ACE16',
    code: 'MO13',
    email: 'john.brown@gmail.com',
    status: 'Active',
    tags: ['developer'],
  },
  {
    key: '2',
    name: 'RECKIT',
    age: 42,
    phone: 'Acetazolamide 250mg tablets',
    address: '1ACE16',
    code: '400-1681',
    email: 'jim.green@gmail.com',
    status: 'Active',
    tags: ['Administrator'],
  },
  {
    key: '3',
    name: 'SOLVAY',
    age: 32,
    phone: 'Acetylcysteine 600mg capsules',
    address: '1ACE16',
    code: '2TRI',
    email: 'joe.black@gmail.com',
    status: 'Active',
    tags: ['Manager'],
  },
  {
    key: '4',
    name: 'NEOLAB',
    age: 32,
    phone: 'Acarbose 50mg tablets',
    address: '1ACE16',
    code: '1FAM42',
    email: 'joe.black@gmail.com',
    status: 'Active',
    tags: ['Manager'],
  }
];

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
  {
    key: 'PO107',
    name: 'A&D INSTRUMENTS LTD',
    qty: '52',
    price: '2999',
    note: '-',
    status: 'Pending',
    tags: ['Manage manual Import','Manage Product', 'Manage Supplier','Manage Price Data','Manage Volume Data','Manage File Import'],
  },
  {
    key: 'PO108',
    name: '5 STAR TECHNOLOGIES LTD',
    qty: '62',
    price: '2599',
    note: '-',
    status: 'Pending',
    tags: ['Manage manual Import','Manage File Import'],
  },
  {
      key: 'PO109',
      name: 'AAH PHARMACEUTICALS LTD',
      qty: '72',
      price: '2499',
      note: '-',
      status: 'Pending',
      tags: ['Manage manual Import','Manage File Import'],
    },
];


const productClassification = [
    { value: "SM_Analysis_Code 1", label: "GENERI" },
    { value: "ETHIC", label: "ETHIC" },
    { value: "H&B", label: "H&B" },
    { value: "SURGIC", label: "SURGIC" },
    // { value: "Quantity", label: "Quantity" },
    // { value: "Dose", label: "Dose" },
  ];
  
  const productFeatures = [
    { value: "UNICHEM", label: "UNICHEM" },
    { value: "UNIDRUG", label: "UNIDRUG" },
    { value: "DALKEIT2", label: "DALKEIT2" },
  ];
  const arr = [], arr1 = []


class SupplierDetails extends Component {
    constructor(props) {
        super(props);
        this.state = {
        form: false,
        productName: '',
        collapsed: false,
        clasiModal: false,
        featuresModal: false,
        purchaseModal: false,
        selected: false,
        prodFeature: [],
        prodFeatureType: '',
        prodFeatureValue: '',
        prodClasi: [],
        prodClasiType: '',
        prodClasiValue: '',
        prefered: false,
        liked: false
        };
      }

    componentDidMount(){
        // console.log("In CDM");
    const prodName = localStorage.getItem("productName");
    if (prodName && prodName != "") {
      this.setState({
        productName: prodName,
      });
    }
    }

    handleBtn = ()=>{
      this.props.history.push('/childProducts');
  }

    handleModalOk = () => {
      // this.setState({ freeModal: false, protectedModal: false });
      if (this.state.clasiModal == true) {
        this.setState({ clasiModal: false,prodClasiType:'', prodClasiValue:'' });
        // this.props.form.resetFields();
      }
      if (this.state.featuresModal == true) {
        this.setState({ featuresModal: false, prodFeatureType: '', prodFeatureValue: '' });
        // this.props.form.resetFields();
      }
    };
  
    handleModalCancel = () => {
      if (this.state.clasiModal == true) {
        this.setState({ clasiModal: false,prodClasiType:'', prodClasiValue:'' });
        // this.props.form.resetFields();
      }
      if (this.state.featuresModal == true) {
        this.setState({ featuresModal: false, prodFeatureType: '', prodFeatureValue: '' });
        // this.props.form.resetFields();
      }
    };

    handleClasiType=(type)=>{

      // console.log("clasi type::",type)
      this.setState({
        prodClasiType: type
      })
    }

    handleClasiValue=(val)=>{

      // console.log("clasi value::",val)
      this.setState({
        prodClasiValue: val
      })
    }

    handleFeatureType=(type)=>{

      // console.log("feature type::",type)
      this.setState({
        prodFeatureType: type
      })
    }

    handleFeatureValue=(val)=>{

      // console.log("feature value::",val)
      this.setState({
        prodFeatureValue: val
      })
    }

    addClasi=()=>{
      // arr = this.state.prodClasi;
      if(this.state.prodClasiType && this.state.prodClasiValue){
        // console.log("add selected::")
        // let arr = []
        let obj={
          type: this.state.prodClasiType,
          value: this.state.prodClasiValue
        }
        arr.push(obj)
        this.setState({
        prodClasi: arr,
        clasiModal: false,
        prodClasiType: '',
        prodClasiValue: '',
      })
      // window.location.reload();
      // this.props.form.resetFields([
      //   "productImg",
      //   "productCaption",
      //   "productType",
      //   "productCategory",
      //   "youtubeLink",
      // ]);
      // this.props.form.resetFields();
    }
    }

    addFeature=()=>{
      // console.log("add selected::", this.state.prodFeatureType,this.state.prodFeatureValue)
      if(this.state.prodFeatureType && this.state.prodFeatureValue){
        // console.log("add selected If::", this.state.prodFeatureType,this.state.prodFeatureValue)
        // let arr1 = []
        let obj={
          type: this.state.prodFeatureType,
          value: this.state.prodFeatureValue
        }
        arr1.push(obj)
        this.setState({
        prodFeature: arr1,
        featuresModal: false,
        prodFeatureType: '',
        prodFeatureValue: '',
      })
      // window.location.reload();
      // this.props.setFieldsValue({
      //   ftype: ' ',
      //   fvalue: ' '
      // });
        // form.resetFields();
      // this.props.form.resetFields([
      //   "type",
      //   "value",
      // ]);
      // this.props.form.resetFields();
    }
    }

    onRemoveClasi=(val,index)=>{
      // console.log("In Remove",val,index)

      let clasiArr = [...this.state.prodClasi];
      // console.log("ClassiArr before",clasiArr);
      clasiArr.splice(index, 1);
      
      // console.log("ClassiArr After",clasiArr);

      this.setState({
          prodClasi: clasiArr,
        });
    }

    onRemoveFeature=(val,index)=>{
      // console.log("In Remove",val,index)

      let featureArr = [...this.state.prodFeature];
      // console.log("ClassiArr before",featureArr);
      featureArr.splice(index, 1);
      
      // console.log("ClassiArr After",featureArr);

      this.setState({
          prodFeature: featureArr,
        });
    }

    onProducts=()=>{
      this.props.history.push('/products')
     }

     onSupplier=()=>{
      this.props.history.push('/supplierDetails')
     }

     goTo=()=>{
      this.props.history.push('/createPO')
     }
    
    render(){

      const columns = [
        {
          title: 'Parent Code',
        //   width: 200,
          render:(data)=>{
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px",cursor:"pointer"}} onClick={()=> {this.onProducts()}}>{data.key}</h3>
            )
          }
          //dataIndex: 'key',
        },
        // {
        //   title: 'DT Type',
        //   // dataIndex: 'name',
        //   key: 'dtType',
        // //   width: 450,
        //   render:(data)=>{
        //     // console.log("Data..",data)
        //     return(
        //       <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.type}</h3>
        //     )
        //   }
        // },
        {
          title: 'Description',
          // dataIndex: 'name',
          key: 'dtDesc',
        //   width: 450,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.desc}</h3>
            )
          }
        },
        {
            title: 'Price',
            // dataIndex: 'name',
            key: 'price',
            sorter: (a, b) => a.price - b.price,
          //   width: 450,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.price}</h3>
              )
            }
          },
          {
            title: 'Forecast',
            // dataIndex: 'name',
            key: 'forecast',
            // sorter: (a, b) => a.price - b.price,
          //   width: 450,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.forecast}</h3>
              )
            }
          },
          {
            title: 'Comment',
            // dataIndex: 'name',
            key: 'comment',
            // sorter: (a, b) => a.price - b.price,
          //   width: 450,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.comment}</h3>
              )
            }
          },
          {
            title: 'Qty',
            // dataIndex: 'name',
            key: 'qty',
            sorter: (a, b) => a.price - b.price,
          //   width: 450,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.qty}</h3>
              )
            }
          },
        // {
        //     title: 'Price From Date',
        //     // dataIndex: 'name',
        //     key: 'createdAt',
        //     // sorter: (a, b) => a.date - b.date,
        //     // width: 450,
        //     render:(data)=>{
        //       // console.log("Data..",data)
        //       return(
        //         <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.date}</h3>
        //       )
        //     }
        //   },
        //   {
        //     title: 'Price Until Date',
        //     // dataIndex: 'name',
        //     key: 'createdAt',
        //     // sorter: (a, b) => a.date - b.date,
        //     // width: 450,
        //     render:(data)=>{
        //       // console.log("Data..",data)
        //       return(
        //         <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.date}</h3>
        //       )
        //     }
        //   },
        
      ];

      const columns1 = [
        // {
        //   title: 'No',
        //   dataIndex: 'key',goTo=()=>{
    //   this.props.history.push('/createPO')
    //  }
        // },
        {
          title: 'Name',
          // dataIndex: 'name',
          key: 'name',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.name}</h3>
            )
          }
        },
        {
          title: 'Prod Code',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}} >{data.code}</h3>
            )
          }
        },
        // {
        //   title: 'Relationship Type',
        //   // dataIndex: 'email',
        //   key: 'code',
        //   render:(data)=>{
        //     // console.log("Data..",data)
        //     return(
        //       <h3 style={{fontWeight:'bold'}}>{data.code}</h3>
        //     )
        //   }
        // },
        {
          title: 'Description',
          // dataIndex: 'email',
          key: 'phone',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.phone}</h3>
            )
          }
        },
        {
          title: 'Related Product Code',
          // dataIndex: 'email',
          key: 'address',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.address}</h3>
            )
          }
        },
        // {
        //   title: 'Action',
        //   key: 'action',
        //   render: (text, record) => (
        //     <>
        //      <Space size="middle">
        //       {/* <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'view'})}} icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}}/>  */}
        //       <Button type="primary" size="large" onClick={this.handleBtn} icon={<EyeOutlined style={{fontSize:25}}/>} style={{marginLeft:"10px",background:"#353b8d",borderColor:"#353b8d"}}
        //       //onClick={() => this.handleMatch(data.prodId)}
        //       /> 
              
        //      </Space>
        //     </>
        //   ),
        // },
      ];

      const PO = [
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
        // {
        //   title: 'Supplier',
        //   // ...this.getColumnSearchProps('name'),
        //   // dataIndex: 'name',
        //   key: 'name',
        //   render:(data)=>{
        //     // console.log("Data..",data)
        //     return(
        //       <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.name}</h3>
        //     )
            
        //   }
        // },
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
          {
            title: 'Created At',
            // ...this.getColumnSearchProps('name'),
            // dataIndex: 'name',
            key: 'date',
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.date}</h3>
              )
              
            }
          },
          // {
          //   title: 'Created By',
          //   // ...this.getColumnSearchProps('name'),
          //   // dataIndex: 'name',
          //   key: 'by',
          //   render:(data)=>{
          //     // console.log("Data..",data)
          //     return(
          //       <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.user}</h3>
          //     )
              
          //   }
          // },
          {
            title: 'Note',
            // dataIndex: 'name',
            key: 'note',
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.note}</h3>
              )
            }
          },
        
      ];
    

      let products = prodsData.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"100%", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Product Code : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.key}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> DT Type : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.type}</span></h4>   
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> DT Description : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.desc}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.price}</span>
               </h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price From Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.date}</span>
               </h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price Until Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.todate}</span></h4>
             </Col>
             </Row>
             </Card>
                  <WhiteSpace size="sm" />
              </List.Item>
          )
      })

        // console.log("In Render Form ",this.props.form);
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
      <App header={
      // <Search placeholder="Search Supplier" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} onSearch={(val)=>this.onSearch(val)}/>
      <Input placeholder="Search Supplier Code/Name " size="large"
      allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
      // onChange={(val)=>this.setState({searchVal: val})}
      // onSearch={(val)=>this.onSearch(val)}
      />
      }>
           {this.state.productName ?
            <h4 
             style={{fontSize:"14px",color:'#213A87',float:"left",marginTop:"1%",marginLeft:"2%",cursor:"pointer"}} onClick={()=> {this.onProducts()}}>
              go to product <span style={{fontSize:"18px",fontWeight:"bold",color:'#213A87',cursor:"pointer"}}>
                            {this.state.productName ? this.state.productName : null}
                           </span>
             </h4>
             : null}
             
            <div style={{marginTop:"0.5%"}}>
            
            <div style={{padding: "5px"}}>
              {/* <Row>
                <Col span={11}> */}

          <Card bordered={false} style={{borderRadius: 5, background: "#eaeaf2",margin:"25px",width:"55%",textAlign:"center",marginLeft:"22%",overflow:"clip",}} >
         
          <Tooltip title="Set as Favourite">
          <HeartTwoTone twoToneColor={this.state.liked ? "#eb2f96" : ""}  style={{float: "right",fontSize:"20px",cursor:"pointer",}} onClick={()=>this.setState({liked: !this.state.liked}) }/>
          </Tooltip>

          <Tooltip title="Set as Preferred">
          <StarTwoTone twoToneColor={this.state.prefered ? " #fae836" : ""} style={{float: "right",fontSize:"20px",marginRight:"1%",cursor:"pointer",}} 
                        onClick={()=>this.setState({prefered: !this.state.prefered}) } />
          </Tooltip>
          
          {/* background: "#ebebff" */}
          {/* <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"20px",marginBottom:"3%"}} >ALPHA</h4>  */}
             {/* color:'#4e4eff' */}
             {/* <Card bordered={false} style={{borderRadius: 5 ,marginTop: 5, background: "#eaeaf2"}}> */}
               <Row>
               <Col span={12}>
                <h4 style={{fontSize: "17px",}}> Supp Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>M013</span></h4>
               </Col>
               <Col span={12}>
                <h4 style={{fontSize: "17px", }}> Name : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>ALPHA</span></h4>   
               </Col>
               </Row>
               {/* <Row>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Stop Ind : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>Account Trading</span></h4>
               </Col>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Currency Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>UK</span></h4>
               </Col>
               </Row> */}

              {/* </Card> */}

           </Card>  
           {/* </Col> */}
           &nbsp;
           
                 <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Products</h4> 

                 <div style={{height:"270px",overflowY:"scroll",whiteSpace:"nowrap",}}> 
                 <Table columns={columns} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={prodsData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />
              </div>

          <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"21px",marginTop:"1%"}}>PO</h4>
            
          <div style={{height:"270px",overflowY:"scroll",whiteSpace:"nowrap",}}> 
            <Table columns={PO} 
                    rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
                    pagination={false}
                    //  expandable={{expandedRowRender: record => <p style={{ margin: 0 }}>{record.name}</p>,rowExpandable: record => record.name !== 'Not Expandable',}} 
                    dataSource={data} style={{margin:'2%'}} className='product' onHeaderRow={(columns, index) => {
                    return {onClick: () => {}, // click header row
            };
            }}/>
            </div>

            </div>
            
            <h4 style={{color:'#213A87', textAlign:'left',fontSize:"15px",marginTop:"1%",marginLeft:"2%",marginBottom:"3%",cursor:"pointer"}}
              onClick={()=>this.goTo()}
            >  
              View All PO</h4> 

            
            
            </div>
          
            
            

              


           
        <Modal
        title="Purchase Order"
        centered
          visible={this.state.purchaseModal}
          onOk={()=>{this.setState({purchaseModal: false})}}
          onCancel={()=>{this.setState({purchaseModal: false})}}
          footer={null}
          width={600}
        >
          <CreatePurchase
              // imageArr={this.state.imgArr}
              // exh={this.props.exh}
              // closeModal={this.handleOk}
            />
        </Modal>
         </App>
         )
        }else{
          return(
          <Appm>
            {/* {console.log("In Mob : ...",)} */}
            <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Supplier</h4> 
            <Card bordered={false} style={{borderRadius: 5, background: "#eaeaf2",margin:"2px",cursor:"pointer"}} >
          {/* background: "#ebebff" */}
          <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"17px",marginBottom:"3%",cursor:"pointer"}} >ALPHA</h4> 
             {/* color:'#4e4eff' */}
             <Card bordered={false} style={{borderRadius: 5 ,marginTop: 5, background: "#eaeaf2"}}>
               <Row>
               <Col span={22}>
                <h4 style={{fontSize: "17px",}}> Supp Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>M013</span></h4>
               </Col>
               </Row>
               <Row>
               <Col span={22}>
                <h4 style={{fontSize: "17px", }}> Name : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>ALPHA</span></h4>   
               </Col>
               </Row>
               <Row>
               <Col span={22}>
               <h4 style={{fontSize: "17px", }}> Stop Ind : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>Account Trading</span></h4>
               </Col>
               </Row>
               <Row>
               <Col span={22}>
               <h4 style={{fontSize: "17px", }}> Currency Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>UK</span></h4>
               </Col>
               </Row>

              </Card>

           </Card>

           <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"17px",marginTop:"5%"}}>Products</h4> 
          <Row style={{marginTop:"10px",height:'300px', overflow:'auto'}}>
                                    <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {products.length > 0 ? 
                                        
                                        products 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                  </Row>
          </Appm>
          )
        }
      }
        }
        </MediaQuery>
     )   
    }

}

export default SupplierDetails