import React, {Component} from "react";
import {Button, Card, Row, Col, Tooltip, Modal, Form, Select, Input, Table, Space} from "antd";
import App from "../App";
// import { withApollo } from "react-apollo";
import CreatePurchase from "./createPurchase"
import {
    AppstoreOutlined,
    PlusOutlined,
    MinusCircleOutlined,
    EyeOutlined,
    EditOutlined,
  } from '@ant-design/icons';
// import Form from "antd/lib/form/Form";

const { Option } = Select;
const {form} = Form
const { Search } = Input;

const priceData = [
  {
    key: 'Tier 1 - Supplier',
    name: 'SPOT',
    code: '5.4',
    fromDate: '15-06-2021',
    toDate: '16-07-2021',
    status: 'Active',
  },
  {
    key: 'Tier 0 - DT Price Type',
    name: 'MORNIN',
    code: '5.5',
    fromDate: '15-06-2021',
    toDate: '16-07-2021',
  },
  {
    key: 'Tier 2 - IRP',
    name: 'RIVOPHA',
    fromDate: '15-06-2021',
    toDate: '16-07-2021',
    code: '2',
    status: 'Active',
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

const grnData = [
  {
    key: '1',
    // suppName: 'ALLPHA',
    prodCode: 'ACE16',
    grnNo: "0",
    prodQty:"200",
    grnQty:"52",
    qtyDesc:"-",
    prodValue:"0",
    grnValue:"0",
    grnPrice:"0",
    receiptDate:"3-3-2017",
    orderDate:"3-3-2017"
  },
  // {
  //   key: '2',
  //   suppName: 'RECKIT',
  //   prodCode: '400-1681',
  //   grnNo: "0",
  //   prodQty:"684",
  //   grnQty:"684",
  //   qtyDesc:"16",
  //   prodValue:"0",
  //   grnValue:"0",
  //   grnPrice:"0",
  //   receiptDate:"6-09-2017",
  //   orderDate:"6-09-2017"
  // },
  // {
  //   key: '3',
  //   suppName: 'NEOLAB',
  //   prodCode: '1FAM42',
  //   grnNo: "4",
  //   prodQty:"0",
  //   grnQty:"100",
  //   qtyDesc:"28",
  //   prodValue:"0",
  //   grnValue:"1200",
  //   grnPrice:"1.2",
  //   orderDate:"20-05-2021",
  //   receiptDate:"20-05-2021",
  // },
  // {
  //   key: '4',
  //   suppName: 'SOLVAY',
  //   prodCode: '2TRI',
  //   grnNo: "2",
  //   prodQty:"4000",
  //   grnQty:"4000",
  //   qtyDesc:"100M",
  //   prodValue:"4800",
  //   grnValue:"4800",
  //   grnPrice:"1.2",
  //   receiptDate:"11-05-2021",
  //   orderDate:"11-05-2021"
  // },
  // {
  //   key: '5',
  //   suppName: 'BELLS',
  //   prodCode: '3ALM50',
  //   grnNo: "1",
  //   prodQty:"1",
  //   grnQty:"-",
  //   qtyDesc:"-",
  //   prodValue:"1",
  //   grnValue:"1",
  //   grnPriceL:"1",
  //   orderDate:"20-05-2021"
  // },
  // {
  //   key: '6',
  //   suppName: 'DEXCEL',
  //   prodeCode: '1ENA20',
  //   grnNo: "1",
  //   prodQty:"1",
  //   grnQty:"-",
  //   qtyDesc:"-",
  //   prodValue:"1",
  //   grnValue:"1",
  //   grnPriceL:"1",
  //   orderDate:"20-05-2021"
  // },
  // {
  //   key: '7',
  //   suppName: 'TILLOMED',
  //   prodCode: '1ENA52',
  //   grnNo: "1",
  //   prodQty:"1",
  //   grnQty:"-",
  //   qtyDesc:"-",
  //   prodValue:"1",
  //   grnValue:"1",
  //   grnPrice:"1",
  //   orderDate:"20-05-2021"
  // },
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


class PricingDetails extends Component {
    constructor(props) {
        super(props);
        this.state = {
        form: false,
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
    // addClassi=(val)=>{
    
    //     let prodVal = val.value
    //     let clasiArr = [...this.state.prodClasi];
    //     let obj = { prodVal };
    //     let index = clasiArr.findIndex((item) => item.prodVal == val.value);
    
    //     // console.log("index", index);
    //     // console.log("ImgArray", clasiArr);
    //     obj.key = clasiArr.length;
    //     if (index == -1) {
    //       // console.log("in if");
    //       clasiArr.push(obj);
    //       // console.log("In If Condition", clasiArr);
    //     } else {
    //       // console.log("in else");
    //       //remove from array
    //       clasiArr.splice(index, 1);
    //       // console.log("In Else Condition", clasiArr);
    //     }
    
    //     this.setState({
    //       prodClasi: clasiArr,
    //     });
    
    //   }
    
      // addFeature = (val) =>{
       
      //   let prodVal = val.value
      //   let clasiArr = [...this.state.prodFeature];
      //   let obj = { prodVal };
      //   let index = clasiArr.findIndex((item) => item.prodVal == val.value);
    
      //   // console.log("index", index);
      //   // console.log("ImgArray", clasiArr);
      //   obj.key = clasiArr.length;
      //   if (index == -1) {
      //     // console.log("in if");
      //     clasiArr.push(obj);
      //     // console.log("In If Condition", clasiArr);
      //   } else {
      //     // console.log("in else");
      //     //remove from array
      //     clasiArr.splice(index, 1);
      //     // console.log("In Else Condition", clasiArr);
      //   }
    
      //   this.setState({
      //     prodFeature: clasiArr,
      //   });
    
      // }

    render(){

      const columns = [
        {
          title: 'Source',
          key: 'key',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.key}</h3>
            )
          }
          // dataIndex: 'key',
        },
        {
          title: 'Supplier',
          // dataIndex: 'name',
          key: 'name',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',cursor:"pointer"}} onClick={()=> {this.onSupplier()}}>{data.name}</h3>
            )
          }
        },
        {
          title: 'Price',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.code}</h3>
            )
          }
        },
        {
          title: 'Forecast',
          // dataIndex: 'email',
          key: 'phone',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px",}}>-</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.phone}</h3>
            )
          }
        },
        {
          title: 'Price From Date',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.fromDate}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        {
          title: 'Price Until Date',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.toDate}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        // {
        //   title: 'Comment',
        //   // dataIndex: 'email',
        //   key: 'address',
        //   render:(data)=>{
        //     // console.log("Data..",data)
        //     return(
        //       <h3 style={{fontWeight:'bold'}}>-</h3>
        //       // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
        //     )
        //   }
        // },
        
      ];

      const columns2 = [
        // {
        //   title: 'No',
        //   dataIndex: 'key',
        // },
        // {
        //   title: 'Supp Code / Name',
        //   // dataIndex: 'email',
        //   key: 'code',
        //   render:(data)=>{
        //     // console.log("Data..",data)
        //     return(
        //       <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.suppName}</h3>
        //     )
        //   }
        // },
        {
          title: 'Prod Code',
          // dataIndex: 'name',
          key: 'pCode',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.prodCode}</h3>
            )
          }
        },
        
        {
          title: 'Grn No',
          // dataIndex: 'email',
          key: 'grnNo',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnNo}</h3>
            )
          }
        },
        {
          title: 'Prod Qty',
          // dataIndex: 'email',
          key: 'pQty',
          sorter: (a, b) => a.prodQty - b.prodQty,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.prodQty}</h3>
            )
          }
        },
        {
          title: 'Grn Qty',
          // dataIndex: 'email',
          key: 'gQty',
          sorter: (a, b) => a.grnQty - b.grnQty,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnQty}</h3>
            )
          }
        },
        {
            title: 'Qty Desc',
            // dataIndex: 'email',
            key: 'Qdesc',
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.qtyDesc}</h3>
              )
            }
          },
          {
            title: 'Prod Value',
            // dataIndex: 'email',
            key: 'pValue',
            sorter: (a, b) => a.prodValue - b.prodValue,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.prodValue}</h3>
              )
            }
          },
          {
            title: 'Grn Value',
            // dataIndex: 'email',
            sorter: (a, b) => a.grnValue - b.grnValue,
            key: 'gValue',
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnValue}</h3>
              )
            }
          },
          {
            title: 'Grn Price',
            // dataIndex: 'email',
            key: 'gPrice',
            sorter: (a, b) => a.grnPrice - b.grnPrice,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.grnPrice}</h3>
              )
            }
          },
          {
            title: 'Order Date',
            // dataIndex: 'email',
            key: 'date',
            // sorter: (a, b) => a.orderDate - b.orderDate,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.orderDate}</h3>
              )
            }
          },
          {
            title: 'Receipt Date',
            // dataIndex: 'email',
            key: 'date',
            // sorter: (a, b) => a.receiptDate - b.receiptDate,
            render:(data)=>{
              // console.log("Data..",data)
              return(
                <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.receiptDate}</h3>
              )
            }
          },
        
      ];

      const columns1 = [
        // {
        //   title: 'No',
        //   dataIndex: 'key',
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
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.code}</h3>
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

        // console.log("In Render Form ",this.props.form);
     return(
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
             style={{fontSize:"14px",color:'#213A87',marginTop:"1%",marginLeft:"2%",cursor:"pointer"}} onClick={()=> {this.onProducts()}}>
              go to product <span style={{fontSize:"18px",fontWeight:"bold",color:'#213A87',cursor:"pointer"}}>
                            {this.state.productName ? this.state.productName : null}
                           </span>
             </h4>
             : null}
           
            <div>
            
          
            <div style={{padding: "10px"}}>
              
                {/* <Col span={11}>

          <Card bordered={false} style={{borderRadius: 5,marginTop:"7%", background: "#eaeaf2"}}>
          {/* background: "#ebebff" *
             <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"20px"}}>ALPHA</h4> 
             {/* color:'#4e4eff' *
             <Card bordered={false} style={{borderRadius: 5 ,marginTop: 5, background: "#eaeaf2"}}>
               <Row>
               <Col span={12}>
                <h4 style={{fontSize: "17px",}}> Supp Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>M013</span></h4>
               </Col>
               <Col span={12}>
                <h4 style={{fontSize: "17px", }}> Name : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>ALPHA</span></h4>   
               </Col>
               </Row>
               <Row>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Stop Ind : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>Account Trading</span></h4>
               </Col>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Currency Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>UK</span></h4>
               </Col>
               </Row>

              </Card>

           </Card>  
           </Col> */}
           
           {/* <Card style={{marginTop:"0.7%"}}> */}
                 <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Pricing</h4> 

                 <Table columns={columns} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={priceData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />

              {/* </Card> */}
           
            </div>

            <div style={{padding: "10px"}}>
              
               <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>GRN</h4> 

               <Table columns={columns2} 
             rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={grnData} 
             style={{margin:'2%'}} 
             className='product' 
             pagination={false}
             />

          </div>
            
           {/* <div style={{padding: "10px"}}>
               <Row>
                <Col span={11}>
          <Card style={{borderRadius: 5,marginTop:"1%", marginTop:"7%", background: "#eaeaf2" }}>
             <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>365HEAL</h4> 
            
             <Card bordered={false} style={{borderRadius: 5 ,marginTop: 5, background: "#eaeaf2"}}>
             <Row >
               <Col span={12}>
                <h4 style={{fontSize: "17px",}}> Supp Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>365HEAL</span></h4>
               </Col>
               <Col span={12}>
                <h4 style={{fontSize: "17px", }}> Name : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>365HEALTHCARE</span></h4>   
               </Col>
               </Row>
               <Row>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Stop Ind : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>Account Trading</span></h4>
               </Col>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Currency Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>UK</span></h4>
               </Col>
               </Row>
               </Card>

           </Card>  
           </Col>
           &nbsp;
           <Col span={12}>
           
                 <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Pricing</h4> 

                 <Table columns={columns} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={priceData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />

             
           </Col>
           </Row> 
            </div>*/}
            
            {/* <div style={{padding: "10px"}}>
              <Row>
                <Col span={11}>
          <Card bordered={false} style={{borderRadius: 5,marginTop:"7%", background: "#eaeaf2"}}>
             <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>365HEAL</h4> 
            
             <Card bordered={false} style={{borderRadius: 5 ,marginTop: 5, background: "#eaeaf2"}}>
             <Row >
               <Col span={12}>
                <h4 style={{fontSize: "17px",}}> Supp Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>365HEAL</span></h4>
               </Col>
               <Col span={12}>
                <h4 style={{fontSize: "17px", }}> Name : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>365HEALTHCARE</span></h4>   
               </Col>
               </Row>
               <Row>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Stop Ind : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>Account Trading</span></h4>
               </Col>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Currency Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>UK</span></h4>
               </Col>
               </Row>
               </Card>

           </Card>  
           </Col>
           &nbsp;
           <Col span={11}>
                 <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Pricing</h4> 

                 <Table columns={columns} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={priceData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />

           </Col>
           </Row>
            </div> */}
            
            
            </div>
           {/* <div style={{flex: "98%",padding: "10px",marginTop:"5%"}}>
              <Card>
                 <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:22,}}>Price</h4> 

                 <Table columns={columns} 
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={priceData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />

              </Card>
            </div> */}
            
            

            {/* <div style={{flex: "98%",padding: "10px"}}>
              <Card>
                  <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:22,}}>Related Products</h4> 

              <Table columns={columns1} 
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={relatedProd} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />
              </Card>
            </div> */}
              


           
         </App>
     )   
    }

}

export default PricingDetails