import React, {Component} from "react";
import {Button, Card, Row, Col, Tooltip, Modal, Form, Select, Input, Table, Space, Divider, Tag, 
        AutoComplete,Tabs, DatePicker, Affix} from "antd";
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
import axios from 'axios';
// import DatePicker from 'react-date-picker';
import moment from 'moment'
import App from "../App";
import Appm from "../mApp"
// import { withApollo } from "react-apollo";
import CreatePurchase from "./createPurchase"
import {
    AppstoreOutlined,
    PlusOutlined,
    PlusCircleOutlined,
    MinusCircleOutlined,
    EyeOutlined,
    EditOutlined,
    LeftOutlined,
    RightOutlined,
    CheckCircleOutlined,
    DoubleRightOutlined,
    DoubleLeftOutlined ,
  } from '@ant-design/icons';
import CreateTasks from "./createTasks";
import MediaQuery from 'react-responsive';
// import Form from "antd/lib/form/Form";

const { Option } = Select;
const {form} = Form
const { Search } = Input;
const { TextArea } = Input;
const { TabPane } = Tabs;

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
    value: 'ACET66',
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
];

const priceData = [
  {
    key: 'Tier 1 - Supplier',
    name: 'SPOT',
    fromDate: '01-06-2021',
    toDate: '31-07-2021',
    code: '5.4',
    status: 'Active',
  },
  {
    key: 'Tier 0 - DT PriceType',
    name: 'MORNIN',
    fromDate: '15-06-2021',
    toDate: '16-07-2021',
    code: '5.5',
    status: 'Active',
  },
  {
    key: 'Tier 2 - IRP',
    name: 'RIVOPHA',
    fromDate: '01-05-2021',
    toDate: '31-07-2021',
    code: '2',
    status: 'Active',
  },
  // {
  //   key: '4',
  //   name: '5 STAR',
  //   age: 32,
  //   phone: '9988776622',
  //   address: 'London, Park Lane no. 2',
  //   code: '5',
  //   email: 'joe.black@gmail.com',
  //   status: 'Active',
  //   tags: ['Manager'],
  // },
  // {
  //   key: '5',
  //   name: 'ABBOTT',
  //   age: 32,
  //   phone: '9988776611',
  //   address: 'London, Park Lane no. 3',
  //   code: '4.8',
  //   email: 'joe.black@gmail.com',
  //   status: 'Active',
  //   tags: ['Manager'],
  // },
  // {
  //   key: '6',
  //   name: 'DEXCEL',
  //   age: 32,
  //   phone: '9988776612',
  //   address: 'London, Park Lane no. 4',
  //   code: '1ENA20',
  //   email: 'joe.black@gmail.com',
  //   status: 'Active',
  //   tags: ['Manager'],
  // },
  // {
  //   key: '7',
  //   name: 'TILLOMED',
  //   age: 32,
  //   phone: '9988776613',
  //   address: 'London, Park Lane no. 5',
  //   code: '1ENA52',
  //   email: 'joe.black@gmail.com',
  //   status: 'Active',
  //   tags: ['Manager'],
  // },
];

const clasData = [
  {
    key: '1',
    name: 'GENERI',
    value: "SM_Analysis_Code_1",
    age: 32,
  },
  // {
  //   key: '2',
  //   name: 'ETHIC',
  //   age: 42,
    
  // },
  {
    key: '3',
    name: 'S-X',
    value: "SM_Analysis_Code_2",
    age: 32,
    },
  {
    key: '4',
    name: 'SPOT',
    value: "SM_Analysis_Code_5",
    age: 32,
  },
  
];



const viewComments = [
  {
    no: 1,
    comment: 'M013 is giving best price for ACAT19.',
    date: '11-05-20221'
  },
  {
    no: 2,
    comment: 'ACAT16 had been added to short-dated.',
    date: '12-05-20221'
  },
  {
    no: 1,
    comment: 'ACAT16 has been added in offer.',
    date: '13-05-20221'
  },
  {
    no: 1,
    comment: 'ACAT31 has been added in offer.',
    date: '14-05-20221'
  },
  {
    no: 1,
    comment: 'ACAT19 has been added in offer.',
    date: '15-05-20221'
  },
  {
    no: 1,
    comment: 'ACE16 has been added in offer.',
    date: '16-05-20221'
  },
  {
    no: 1,
    comment: 'ACAT31 has been added in offer.',
    date: '17-05-20221'
  },
];

const featureData =  [
  {
    key: '1',
    name: 'DT Pack',
    value: '60',
    age: 32,
    
  },
  {
    key: '2',
    name: 'Temprature',
    value: 'AMBIENT',
    age: 42,
    
  },
  {
    key: '3',
    name: 'List Currency',
    value: 'UK',
    age: 32,
    },
  // {
  //   key: '4',
  //   name: 'Buyer',
  //   age: 32,
  // },
];

const relatedProd = [
  {
    key: '1',
    name: 'ALPHA',
    phone: 'Aceclofenac 100mg tablets 60 tablet',
    pcode: '1ACE16',
    code: 'MO13',
    rType: 'Child',
  },
  {
    key: '2',
    name: 'ALPHA',
    phone: 'Aceclofenac 100mg tablets 60 tablet',
    pcode: '120-6564',
    code: '400-1681',
    rType: 'Child',
  },
  {
    key: '3',
    name: 'ALPHA',
    phone: 'Aceclofenac 100mg tablets 60 tablet',
    pcode: '120-6564',
    code: '400-1681',
    rType: 'Common Supplier',
  },
  // {
  //   key: '4',
  //   name: 'SOLVAY',
  //   phone: 'Acetylcysteine 600mg capsules',
  //   pcode: '1ACE16',
  //   code: '2TRI',
  //   email: 'Common Supplier',
  // },
  // {
  //   key: '5',
  //   name: 'NEOLAB',
  //   phone: 'Acarbose 50mg tablets',
  //   pcode: '1ACE16',
  //   code: '1FAM42',
  //   email: 'Common Supplier',
  // }
];

const tags = [
  { value: "Out Of Stock", label: "Out Of Stock" },
  { value: "In Offer", label: "In Offer" },
  { value: "Short Dated", label: "Short Dated" },
];

const productClassification = [
    { value: "GENERI", label: "GENERI" },
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

  const group = [
    { value: "Manager", label: "Manager" },
    { value: "Buyer", label: "Buyer" },
    { value: "Staff", label: "Staff" },
    { value: "Supplier", label: "Supplier" },
  ];

  const prodHistory = [
    {
      key: 'Price Cohession',
      current: '£ 5.00',
      m1: '£ 7.0',
      m2: '£ 6.0',
      m3: '£ 8.0',
      avg: '£ 7.0',
    },
    {
      key: 'Usage',
      current: '1000',
      m1: '1001',
      m2: '1002',
      m3: '1003',
      avg: '1002',
    },
    {
      key: 'PCA',
      current: '2000',
      m1: '2001',
      m2: '2002',
      m3: '2003',
      avg: '2002',
    },
  ]

  const prodCapture = [
    {
      source: 'Drug Tarrif (DT) Prices',
      price: ' 5.00',
      nprice: ' 4.50',
      forecast: '-',
    },
      {
        source: 'Supplier Pricing',
        price: ' 7.00',
        nprice: ' 7.00',
        forecast: '--',
      },
      {
        source: 'Independent Retail Pharmacy (IRP)',
        price: ' 6.00',
        nprice: ' 5.80',
        forecast: '-',
      },
      {
        source: 'IRP / Buying Group Tender pricing',
        price: ' 6.50',
        nprice: ' 6.40',
        forecast: '-',
      },
      {
        source: 'IRP Day-to-day Pricing / Offers',
        price: ' 5.00',
        nprice: ' 7.00',
        forecast: '-',
      },
      {
        source: 'Wavedata',
        price: ' 7.00',
        nprice: ' 6.00',
        forecast: '-',
      },
      {
        source: 'PHD',
        price: ' 5.00',
        nprice: ' 5.00',
        forecast: '-',
      },
  ]

  const Grn = [
    {
      childCode: '1ACAR59',
      supplier: ' 5.00',
      gPrice: ' 7.0',
      gQty: ' 6.0',
    },
      {
        childCode: '117-7492',
        supplier: ' 5.00',
        gPrice: ' 7.0',
        gQty: ' 6.0',
      },
      {
        childCode: '115-7494',
        supplier: ' 5.00',
        gPrice: ' 7.0',
        gQty: ' 6.0',
      },
      {
        childCode: '123-7924',
        supplier: ' 5.00',
        gPrice: ' 7.0',
        gQty: ' 6.0',
      },
      {
        childCode: '123-7924',
        supplier: ' 5.00',
        gPrice: ' 7.0',
        gQty: ' 6.0',
      },
      {
        childCode: '123-7924',
        supplier: ' 5.00',
        gPrice: ' 7.0',
        gQty: ' 6.0',
      },
      {
        childCode: '123-7924',
        supplier: ' 5.00',
        gPrice: ' 7.0',
        gQty: ' 6.0',
      },
  ]

  const inventory = [
    {
      childCode: '117-7492',
      phyStock: '100',
      allocStock: '0',
      onOrder: '0',
      allocAfter: '0',
      stdCost: "3.15"
    },
    {
      childCode: '123-7924',
      phyStock: '19',
      allocStock: '0',
      onOrder: '0',
      allocAfter: '0',
      stdCost: "4.15"
    },
    {
      childCode: '115-7494',
      phyStock: '15',
      allocStock: '0',
      onOrder: '0',
      allocAfter: '0',
      stdCost: "5.00"
    },
    {
      childCode: '1ACAR59',
      phyStock: '20',
      allocStock: '0',
      onOrder: '0',
      allocAfter: '0',
      stdCost: "3.60"
    },
    {
      childCode: '1ACAR59',
      phyStock: '25',
      allocStock: '0',
      onOrder: '0',
      allocAfter: '0',
      stdCost: "4.80"
    },
    {
      childCode: '1ACAR59',
      phyStock: '25',
      allocStock: '0',
      onOrder: '0',
      allocAfter: '0',
      stdCost: "4.80"
    },
  ]


  const arr = [], arr1 = []

  const children = [];
      for (let i = 10; i < 15; i++) {
        children.push(<Option key={i.toString(36) + i}>{i.toString(36) + i}</Option>);
      }

class Products extends Component {
    constructor(props) {
        super(props);
        this.state = {
        form: false,
        tableData:[],
        userDetails: [],
        collapsed: false,
        clasiModal: false,
        featuresModal: false,
        purchaseModal: false,
        taskModal: false,
        selected: false,
        commentModal: false,
        tagModal: false,
        taskView: false,
        prodFeature: [],
        prodFeatureType: '',
        prodFeatureValue: '',
        prodClasi: [],
        prodClasiType: '',
        prodClasiValue: '',
        fullPC: false,
        fullGRN: false,
        affixed: false
        };
      }

    componentDidMount(){
        // console.log("In CDM");
        // localStorage.setItem('productName', 'ACE16');

        let getURL= 'https://api.sigmaproductmaster.webdezign.uk/api/products'
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
        }else{
          this.setState({
            userDetails: userData
          })
        }
    }

    handleBtn = ()=>{
      this.props.history.push('/childProducts');
  }

  onDateChange(date) {
    console.log(":::::",date);
    // that.setState({
    //     date: Math.floor(date.valueOf()/1000)
    // })
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

    onGoto=()=>{
      this.props.history.push('/pricingDetails')
     }

     onSupplier=()=>{
      this.props.history.push('/supplierDetails')
     }

     onSearch=(n)=>{
      // console.log("In Search...",n)
      let arr = [];
      this.state.prodData.map((i, j) => {
       //  console.log("....",i)
        if (i.parent_product_code.toLowerCase().includes(n.toLowerCase())
         ) {
           let obj={
             value : i.parent_product_code 
           }
           arr.push(obj);
        }
        if (i.clean_description.toLowerCase().includes(n.toLowerCase()) 
        ) {
          let obj={
            value : i.clean_description 
          }
          arr.push(obj);
       }
      });
       // console.log("Search Index:: ",index)
      // console.log("Search Name UM:: ",arr)
       this.setState({
         tableData: arr
       })
       if(n == ''){
        // console.log("Search No Result:: ")
         
         this.setState({
           tableData: []
         })
       }
     }

      onSelected=(option)=>{
        // console.log("In Selected..",option)

        //  this.props.history.push('/products')
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
          title: 'Tier-Source',
          key: 'key',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold'}} >{data.key}</h3>
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
              <h3 style={{fontWeight:'bold',fontSize:"15px",cursor:"pointer"}} onClick={()=> {this.onSupplier()}}>{data.name}</h3>
            )
          }
        },
        {
          title: 'Price',
          // dataIndex: 'email',
          sorter: (a, b) => a.code - b.code,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}} >{data.code}</h3>
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
              <h3 style={{fontWeight:'bold',fontSize:"15px"}} >-</h3>
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
              <h3 style={{fontWeight:'bold',fontSize:"15px"}} >{data.fromDate}</h3>
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
              <h3 style={{fontWeight:'bold',fontSize:"15px"}} >{data.toDate}</h3>
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

      const columns1 = [
        // {
        //   title: 'No',
        //   dataIndex: 'key',
        // },
        // {
        //   title: 'Supp Name',
        //   // dataIndex: 'name',
        //   key: 'name',
        //   render:(data)=>{
        //     // console.log("Data..",data)
        //     return(
        //       <h3 style={{fontWeight:'bold'}}>{data.name}</h3>
        //     )
        //   }
        // },
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
          title: 'Relationship Type',
          // dataIndex: 'email',
          key: 'rType',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.rType}</h3>
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

      const viewComment = [
    
        {
          title: 'Comments',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.comment}</h3>
            )
          }
        },
        {
          title: 'Date',
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.date}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        
      ];

      const productHistory = [
        
        {
          // title: 'Current Month',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.key}</h3>
            )
          }
        },
        {
          title: 'M(Current Month)',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Current Month </div>
          // )},
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.current}</h3>
            )
          }
        },
        {
          title: 'M-1',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.m1}</h3>
            )
          }
        },
        {
          title: 'M-2',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.m2}</h3>
            )
          }
        },
        {
          title: 'M-3',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.m3}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        {
          title: 'Average',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Average </div>
          // )},
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.avg}</h3>
            )
          }
        },
        
      ];

      const productCapture = [
        
        {
          title: 'Source',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.source}</h3>
            )
          }
        },
        
        {
          title: 'Price (Latest)',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          key: 'code',
          sorter: (a, b) => a.price - b.price,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>£{data.price}</h3>
            )
          }
        },
        {
          title: 'Negotiated Price',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.nprice - b.nprice,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>£{data.nprice}</h3>
            )
          }
        },
        {
          title: 'Forecast',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.forecast}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        
      ];

      const GRN = [
        
        {
          title: 'Child Code',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.childCode}</h3>
            )
          }
        },
        {
          title: 'Supplier',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Current Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.supplier - b.supplier,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>£{data.supplier}</h3>
            )
          }
        },
        {
          title: 'Grn Price',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.gPrice - b.gPrice,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>£{data.gPrice}</h3>
            )
          }
        },
        {
          title: 'Grn Qty',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.gQty - b.gQty,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.gQty}</h3>
            )
          }
        },
      ];

      const Inventory = [
        
        {
          title: 'Child Code',
          // dataIndex: 'email',
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.childCode}</h3>
            )
          }
        },
        {
          title: 'Phys Stock',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Current Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.phyStock - b.phyStock,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.phyStock}</h3>
            )
          }
        },
        {
          title: 'Alloc Stock',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.allocStock - b.allocStock,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.allocStock}</h3>
            )
          }
        },
        {
          title: 'Alloc After',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.allocAfter - b.allocAfter,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.allocAfter}</h3>
            )
          }
        },
        {
          title: 'On Order',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Previous Month </div>
          // )},
          // dataIndex: 'email',
          key: 'date',
          // sorter: (a, b) => a.date - b.date,
          sorter: (a, b) => a.onOrder - b.onOrder,
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.onOrder}</h3>
              // <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
            )
          }
        },
        {
          title: 'Std Cost',
          // title: () =>{return(
          //   <div style={{fontSize:"17px",color:"#213A87",fontWeight:"bold"}}> Average </div>
          // )},
          // dataIndex: 'email',
          sorter: (a, b) => a.stdCost - b.stdCost,
          key: 'code',
          render:(data)=>{
            // console.log("Data..",data)
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.stdCost}</h3>
            )
          }
        },
        
      ];


      let products = priceData.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"100%", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Tier-Source : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.key}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> Supplier : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>   
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.code}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price From Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.fromDate}</span>
               </h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price Until Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.toDate}</span></h4>
             </Col>
             </Row>
             </Card>
                  <WhiteSpace size="sm" />
              </List.Item>
          )
      })

      let productsCapta = prodCapture.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"300px", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Source : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.source}</span></h4>
             </Col>
             </Row>
             {/* <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> Supplier : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>   
             </Col>
             </Row> */}
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price(Latest) : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.price}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Negotiated Price : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.nprice}</span>
               </h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Forecast : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.forecast}</span></h4>
             </Col>
             </Row>
             </Card>
                  <WhiteSpace size="sm" />
              </List.Item>
          )
      })

      let productsGrn = Grn.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"300px", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Child Code : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.childCode}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> Supplier : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.supplier}</span></h4>   
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Grn Price : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.gPrice}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Grn Qty: &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.gQty}</span>
               </h4>
             </Col>
             </Row>
             {/* <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price Until Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.toDate}</span></h4>
             </Col>
             </Row> */}
             </Card>
                  <WhiteSpace size="sm" />
              </List.Item>
          )
      })

      let productsInventory = inventory.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"300px", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Child Code : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.childCode}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> Phy Stock : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.phyStock}</span></h4>   
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Alloc Stock : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.allocStock}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Alloc After : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.allocAfter}</span>
               </h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> On Order : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.onOrder}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Std Cost : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.stdCost}</span></h4>
             </Col>
             </Row>
             </Card>
                  <WhiteSpace size="sm" />
              </List.Item>
          )
      })

      let rproducts = relatedProd.map((p)=>{
        // console.log("record..",p)
          return(
              <List.Item style={{marginBottom:'5px'}}>
                <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"100%", background: "#eaeaf2"}}> 
           
           <Row >
             <Col span={24}>
              <h4 style={{fontSize: "14px",}}> Prod Code : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.code}</span></h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
              <h4 style={{fontSize: "14px", }}> Description : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.phone}</span></h4>   
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Relationship Type : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.rType}</span></h4>
             </Col>
             </Row>
             {/* <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price From Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.fromDate}</span>
               </h4>
             </Col>
             </Row>
             <Row>
             <Col span={24}>
             <h4 style={{fontSize: "14px", }}> Price Until Date : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.toDate}</span></h4>
             </Col>
             </Row> */}
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
          <AutoComplete style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius: 5}}
            options={this.state.tableData.length > 0 ? this.state.tableData : ""}
            placeholder="Search Prod Code / DT Description"
            // filterOption={(inputValue, option) => option.value.toUpperCase().indexOf(inputValue.toUpperCase()) !== -1
          // }
            //  onChange={(e)=>this.onSearch(e)}
            // onSelect={(option)=>this.onSelected(option)}
            // onSearch={(val)=>this.onSearch(val)}     
          />
        //  <Search placeholder="Search  Products , Supplier , DT Desc" size="large"
        //  allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}}/>
         
         }>
           {/* <Row>
             <Col span={20}>
             </Col>
             <Col span={3}>
           <Button size="middle" type="primary" onClick={()=>{this.setState({purchaseModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d',marginTop:"0.5%",borderRadius:5,marginRight:"15%"}}>
                  Create Purchase Order
            </Button>
            </Col> */}
            {/* <Col span={1}>
           <Button size="middle" type="primary" onClick={()=>{this.setState({taskModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d',marginTop:"0.5%",borderRadius:5,marginRight:"15%"}}>
                  Create Task
            </Button>
            </Col> */}
            {/* </Row> */}
          
             {/* <div> */}
             {/* <Row style={{marginTop:"1%"}}>
             
             <Col span={7} offset={6}> */}
           {/* <div style={{width: "33%"}}>*/}
           {/* <Search placeholder="Search  Products , Supplier , DT Desc" size="large"
        
         allowClear style={{width:'30%'}} /> */}
         {/* </div>  */}
         
         {/* </Col>
         <Col span={2} offset={6}> */}
            {/* <div style={{width: "33%"}}> */}
               
            {/* </div> */}
            {/* </Col>
            </Row> */}
            {/* </div> */}
            
            <div>
            <div>
              {/* <Affix onChange={(affixed) => this.setState({affixed})}> */}
                {/* <Card bordered={false} 
                style={{borderRadius: 5 ,margin: "1%",height:"120px", background: this.state.affixed ? 'rgba(52, 52, 52, 0.8)' : "",
               }}
                > */}
                  <Card bordered={false} style={{borderRadius: 5 ,height:"80px", background: this.state.affixed ? '#b6b6c2' :"#dfdfee",
                       width:"55%",textAlign:"center",marginLeft:"22%",overflow:"clip",marginTop:"1%" }}>

                           {/* CSS: position: "sticky",top: 0 */}

                <Row style={{marginTop:"-0%"}}>
                
               <Col span={6}>
                <h4 style={{fontSize: "17px",
                //color: this.state.affixed ? 'white' :""
                }}> Product Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold',
                //color: this.state.affixed ? 'white' :""
                }}>ACE16</span></h4>
               </Col>
               <Col span={15}>
                <h4 style={{fontSize: "17px", 
                //color: this.state.affixed ? 'white' :""
                }}> DT Description : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold',
                //color: this.state.affixed ? 'white' :""
                }}>ACECLOFENAC 100MG TABLETS</span></h4>   
               </Col>
               <Col span={1} offset={2}>
               <CheckCircleOutlined style={{fontSize: "20px",color:'#27ae60',float:"right"}}/>
               </Col>
               </Row>
               </Card>
                {/* </Card> */}
              {/* </Affix> */}
              </div> 
                <div>
          <Row>

           <Col span={12}>
            <div style={{padding: "10px"}}>
          {/* <Card bordered={false} style={{borderRadius: 5,marginTop:"1%"}}> */}
             
             {/* <Button type="primary" onClick={()=>{this.setState({clasiModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#A1C3D1', float: "right", margin: -5}}>
                  Create Purchase Order
            </Button> */}
            {/* <h4 style={{color:'#213a87', textAlign:'center', fontWeight:'bold',fontSize:"20px",marginBottom:"3%"}}>Overview</h4>  */}
              


              <div style={{}}>
             <Card bordered={false} style={{borderRadius: 5 ,marginTop: "2%",height:"350px", background: "#dfdfee"}}> 
             {/* background: "#feeeee" */}
             <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"20px",marginBottom:"3%"}}>Overview</h4> 
             {/* color:'#ff5757', */}
             {/* <Row >
               <Col span={12}>
                <h4 style={{fontSize: "17px",}}> Product Code : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>ACE16</span></h4>
               </Col>
               <Col span={12}>
                <h4 style={{fontSize: "17px", }}> DT Description : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>ACECLOFENAC 100MG TABLETS</span></h4>   
               </Col>
               </Row>
               <Row>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> DT Type : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>Tablet</span></h4>
               </Col>
               <Col span={12}>
               <h4 style={{fontSize: "17px", }}> Status : &nbsp; <span style={{fontSize: "17px", fontWeight:'bold'}}>
                 <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="green">Live</Tag> /  &nbsp;<Tag style={{fontSize: "17px", fontWeight:'bold'}} color="red">Discontinued</Tag></span></h4>
               </Col>
               </Row> */}
               {/* <br/>
               <Divider />
               <br/> */}
               <div style={{marginTop:"5%"}}>
               <Row gutter={[16, 16]}>
                  <Col span={4}>

                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}>M (Current Month)</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}>M-1</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}>M-2</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}>M-3</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}>Average</h4>
                  </Col>
                  </Row>
                  <Row style={{marginTop:"2%"}}>
                  <Col span={4}>
                   <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}> 
                   Price Concession
                   </h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>£ 5.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>£ 7.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>£ 6.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>£ 8.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>£ 7.00</h4>
                  </Col>
                </Row>
                <Row>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}>Usage</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>1000</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>1001</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>1002</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>1003</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>1002</h4>
                  </Col>
                </Row>
                <Row>
                  <Col span={4}>
                   <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"17px"}}> PCA </h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>2000</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>2001</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>2002</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>2003</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"17px"}}>2002</h4>
                  </Col>
                </Row>
                </div>

               {/* <Table columns={productHistory} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
                 dataSource={prodHistory} 
                 pagination={false}
            //  style={{margin:'2%'}} 
                 className='product'
                /> */}
               </Card>

               </div>
              {/* <Divider /> */}

               {/* <Row style={{marginTop:35}}> */}
               {/* <Table columns={productHistory} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
                 dataSource={prodHistory} 
                 pagination={false}
            //  style={{margin:'2%'}} 
                 className='product'
                /> */}
               {/* </Row> */}


                {/* <Col span={11}>
                <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"20px",marginBottom:"4%"}}>Classification</h4>
                <Card bordered={false} style={{borderRadius: 5, marginTop: 5,background: "#eaeaf2"}}> */}
                  {/* <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"20px",marginBottom:"4%"}}>Classification</h4> */}
                {/*<div style={{ float: "right"}}>
                 <Tooltip title="Add Classification">
                <Button type="primary" shape="circle" icon={<PlusOutlined />} onClick={()=>{this.setState({clasiModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d'}}/> 
               </Tooltip>
               </div>*/}
              {/* <div style={{marginTop: 5,}}>
                  
                                  <Row style={{marginBottom: 15,marginLeft: 35}}>
                                     <Col>
                                     <span
                                      style={{cursor: "pointer",marginTop: -9,color:'#48486c',
                                      marginLeft: 5,fontSize: "15px"
                                      }}
                                    >
                                      Type
                                    </span> 
                                    </Col>
                                    <Col offset={13}>
                                    {/* <span
                                      style={{cursor: "pointer",marginTop: -9,color:'#48486c',
                                      marginLeft: 5,fontSize: "15px"
                                      }}
                                    >
                                      Value
                                    </span> *
                                    </Col>
                                    </Row>*/}
                                    {/* // } */}

                             {/* {clasData &&
                              clasData.map((p,i) => {
                                // console.log("In Map",p)
                                return (
                                  <>
                                
                                  <Row gutter={24} style={{marginBottom: 5,marginLeft:5}}>
                                    
                                    <span
                                      style={{
                                        display: "inline-block",
                                        height: "8px",
                                        width: "8px",
                                        background: '#48486c',
                                        marginRight: "8px",
                                        marginTop: 7,
                                      }}
                                    ></span>
                                    {/* "#001529" *
                                    <Col span={14}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -25,fontWeight: 'bold',
                                      marginLeft: 5,fontSize: "15px"
                                      }}
                                    >
                                      {p.value}
                                    </span>
                                    </Col>
                                    <Col span={8}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -35,fontWeight: 'bold',
                                      marginLeft: 5,fontSize: "15px"
                                      }}
                                    >
                                      {p.name}
                                    </span>
                                    </Col>
                                   <Col span={2}>
                                    <MinusCircleOutlined style={{fontSize: 22,color:"#D2042D",}} onClick={()=>this.onRemoveClasi(p,i)}/>
                                    </Col> 
                                 </Row>
                                 </>
                                );
                              })}*/}

                            {/* {!this.state.prodClasi && (
                              <Row style={{ marginBottom: "10px" }}>
                                <p>No products available</p>
                              </Row>
                            )}
                    </div>
                </Card>
                </Col> */}
                {/* <Col span={11} offset={1}>
                <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize: "20px",marginBottom:"4%"}}>Features</h4>
                <Card bordered={false} style={{borderRadius: 5,marginTop: 5,background: "#eaeaf2"}}> 
                <div style={{ float: "right"}}>
                <Tooltip title="Add Features">
                <Button type="primary" shape="circle" icon={<PlusOutlined />} onClick={()=>{this.setState({featuresModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d'}}
                /> 
               </Tooltip>
               </div>
               <div style={{marginTop: 5}}>
               <Row style={{marginBottom: 15,marginLeft: 35}}>
                                     <Col>
                                    {/* <span
                                      style={{cursor: "pointer",marginTop: -9,color:'#48486c',
                                      marginLeft: 5,fontSize: "15px"
                                      }}
                                    >
                                      Type
                                    </span> *
                                    </Col>
                                    <Col offset={11}>
                                    {/* <span
                                      style={{cursor: "pointer",marginTop: -9,color:'#48486c',
                                      marginLeft: 5,fontSize: "15px"
                                      }}
                                    >
                                      Value
                                    </span> *
                                    </Col>
                                    
                                    </Row>
                             {/* } *
                            {featureData &&
                              featureData.map((p,i) => {
                                return (
                                  <Row gutter={24} style={{marginBottom: 5,marginLeft: 5}}>
                                    <span
                                      style={{
                                        display: "inline-block",
                                        height: "8px",
                                        width: "8px",
                                        background: '#48486c', //#a9dbfc
                                        marginRight: "8px",
                                        marginTop: 7,
                                      }}
                                    ></span>
                                     <Col span={13}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -25,fontWeight: 'bold',fontSize: "15px"
                                      }}
                                    >
                                      {p.name}
                                    </span>
                                    </Col>
                                    <Col span={8}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -35,fontWeight: 'bold',fontSize: "15px"
                                      }}
                                    >
                                      {p.value}
                                      {/* {p.value} *
                                    </span>
                                    </Col>
                                    {/* <Col span={2}>
                                    <MinusCircleOutlined style={{fontSize: 22,color:"#D2042D",}} onClick={()=>this.onRemoveFeature(p,i)}/>
                                    </Col> *
                                 </Row>
                                );
                              })}

                            {/* {!this.state.prodFeature && (
                              <Row style={{ marginBottom: "10px" }}>
                                <p>No products available</p>
                              </Row>
                            )} *
                    </div>
                    
                </Card>
                </Col>*/}
               {/* </Row> */}

               {/* <div style={{textAlign:"center"}}>
               <Button size="large" type="primary" onClick={()=>{this.setState({purchaseModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Create Purchase Order
            </Button>
            </div> */}

           {/* </Card>   */}
            </div>
            </Col>

            <Col span={12}>

            <Card 
            bordered={false} 
            style={{borderRadius: 5,marginTop:"3%",background: "#dfdfee",height:"350px",overflowY:"scroll",whiteSpace:"nowrap"}}
            >
              <Row>
                <Col span={20}>
              <h4 style={{color:'#213a87',fontWeight:'bold',fontSize: "19px",marginBottom:"0.5%"}}>Comment</h4>
                </Col>
                <Col>
              
              <Button size="small" type="primary" onClick={()=>{this.setState({commentModal: true})}}
                style={{backgroundColor:'#353b8d',fontSize:"12px", borderColor:'#353b8d',borderRadius:5}}>
                  Add Comment
            </Button>
            </Col> 
              </Row>


              <Row>
              {/* <TextArea rows={4} /> */}

              <h4 style={{float: "left", fontSize: "16px", margin: 5,}}><span style={{fontWeight:"bold"}}>M013</span> is giving best price for ACAT19.</h4>
              
              <h4 style={{float: "left", fontSize: "16px", margin: 5,}}></h4>
            </Row>
            <Row>  
            <h4 style={{float: "left", fontSize: "16px", margin: 5,}}><span style={{fontWeight:"bold"}}>ACAT16</span> had been added to short-dated.</h4>
            
            </Row>
            {/* <Row>  
            <h4 style={{float: "left", fontSize: "16px", margin: 5,}}><span style={{fontWeight:"bold"}}>ACAT16</span> has been added in offer.</h4>
            </Row> */}
            <Row style={{marginTop:"7%"}}>
              <Col span={22}>
              <Button type="link" 
                 onClick={()=>{this.setState({commentView: true})}}
                style={{color:'#213a87',fontWeight:"bold",fontSize:"17px", borderRadius:5}}>
                  View More
            </Button>
              </Col>
              <Col>
              {/* <PlusCircleOutlined style={{fontSize: "25px",color:'#213a87'}} onClick={()=>{this.setState({commentModal: true})}}/> */}
            {/* <Button type="primary" onClick={()=>{this.setState({commentModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,borderRadius:5}}>
                  Add Comment
            </Button> */}
            </Col> 
            </Row>
            {/* <br/> */}
            <Divider/>
            {/* <br/> */}
            <Row>
              <Col span={20}>
              <h4 style={{color:'#213a87', float: "left", fontWeight:'bold',fontSize: "19px",marginBottom:"0.5%"}}> Tags</h4>
              </Col>
              <Col>
              
              <Button size="small" type="primary" onClick={()=>{this.setState({tagModal: true})}}
                style={{backgroundColor:'#353b8d',fontSize:"12px", borderColor:'#353b8d',borderRadius:5}}>
                  Attact Tag
            </Button>
            </Col> 
              </Row>

              <Tabs defaultActiveKey="1">
    <TabPane tab="Current" key="1">
               <Row>
                  <Col span={6}>
                    <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="orange">In Offer</Tag>
                  </Col>
                  <Col>

                  <h4 style={{float: "left", fontSize: "16px",}}> Expiring in 8 days .</h4>

                  </Col>
                  {/* <Col span={4}>
                    <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="red">Out Of Stock</Tag>
                  </Col> */}
                </Row>
                
    </TabPane>
    <TabPane tab="Historical" key="2">
      <Row>
    <Col span={6}>
                    <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="red">Out Of Stock</Tag>
                  </Col>
                  
                  <Col>

                  <h4 style={{float: "left", fontSize: "16px",}}> 01/08/2021 - 31/08/2021</h4>
                  
                  </Col>
                  
                  </Row>
                  <Row style={{marginTop:"1%"}}>
    <Col span={6}>
                    <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="red">DNQ</Tag>
                  </Col>

                  <Col>

                  <h4 style={{float: "left", fontSize: "16px",}}> 01/07/2021 - 31/07/2021</h4>
                  
                  </Col>
                  </Row>
    </TabPane>
    
  
                
                </Tabs>
                <Row style={{marginTop:"7%"}}>
              <Col span={22}>
            
              </Col>
              <Col>
              {/* <PlusCircleOutlined style={{fontSize: "25px",color:'#213a87'}} onClick={()=>{this.setState({tagModal: true})}}/> */}
         
            </Col> 
            </Row>
            {/* <Select  style={{ width: '100%',marginTop:"2%" }} placeholder="Select Tags" mode="multiple"
            // onChange={this.handleChange}
            >
             {tags.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
            </Select> */}

            {/* <Button type="primary" 
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,borderRadius:5}}>
                  Attach Tags
            </Button> */}
            
            </Card>

            {/* <Row style={{marginTop:"7%"}}>
              <Col span={17}>
              <Button size="middle" type="primary" onClick={()=>{this.setState({taskModal: true})}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d',marginTop:"0.5%",borderRadius:5,marginRight:"15%"}}>
                  Create Task
            </Button>
             
              </Col>
              <Col>
              <Button type="link" 
                 onClick={()=>{this.setState({taskView: true})}}
                style={{color:'#48486c',fontWeight:"bold",fontSize:"17px", borderRadius:5}}>
                  View Tasks
            </Button>
              
            </Col> 
            </Row> */}
            

            </Col>
            </Row>
            {/* <div style={{flex: "30%",padding: "10px"}}>
            <Card>
                 <h4 style={{color:'#213A87', textAlign:'center', fontWeight:'bold',fontSize:22,}}>Price</h4> 

                 <Table columns={columns} 
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={priceData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' />

              </Card>
            </div>
            </div> */}

            
            </div>
           {/* <div style={{flex: "98%",padding: "10px"}}> */}
              {/* <Card> */}
                 {/* <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Pricing</h4>  */}
{/* // #213A87' */}
                 {/* <Table columns={columns} bordered={true}
                 rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
                //  rowClassName={(record, index) => onClick={()=> {this.onGoto()}}}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={priceData} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product'
            //  onRow={(record, rowIndex) => {
            //   return {
            //     onClick: event => {}, // click row
            //     onDoubleClick: event => {}, // double click row
            //     onContextMenu: event => {}, // right button click row
            //     onMouseEnter: event => {}, // mouse enter row
            //     onMouseLeave: event => {}, // mouse leave row
            //   }; 
             
             />
             <h2 style={{color:'#48486c',marginLeft:"92%",fontSize:"18px",marginTop:"-1%",cursor:"pointer"}} 
             onClick={()=> {this.onGoto()}}>
               View More
               </h2> */}
{/* '#213A87' */}
              {/* </Card> */}
            {/* </div> */}

            <div style={{display: "flex",marginTop:"1%"}}>

            {/* <Row gutter={[16, 16]}>  */}
                {/* <Col span={this.state.fullPC == true ? 22 : 11}> */}
                {this.state.fullGRN != true && 
               <div style={{width: this.state.fullPC == true ? "100%" : "50%",height:"350px",}}>
                <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Price Capture</h4> 
                {/* <Button type="primary" shape="circle" icon={<RightOutlined style={{fontSize:"22px"}}/>} 
                    style={{marginLeft:"10px", background:"#353b8d", borderColor:"#353b8d"}}
                    onClick={()=>this.setState({fullPC: !this.state.fullPC})}
              />   */}
              
              <div style={{height:"270px",overflowY:"scroll",whiteSpace:"nowrap",}}>
              <Table 
                  columns={productCapture} 
                  bordered={true}
                  rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
                  // rowClassName={(record, index) => index === 0 ? "table-row-dark1" : "table-row-dark"}
                //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
                dataSource={prodCapture} 
                pagination={false}
                style={{margin:'1%'}} 
                className='product' 
                />
                </div>
                {/* <DoubleRightOutlined onClick={()=>this.setState({fullPC: !this.state.fullPC})}/> */}
             </div>
             }
                {/* </Col> */}

               
                  <div style={{alignSelf:"center",content: "",display: "table"}}>
                    
                  {this.state.fullGRN != true && 
                    <div style={{width:"5%"}}>
                      {this.state.fullPC != true ? 
                        <DoubleRightOutlined onClick={()=>this.setState({fullPC: !this.state.fullPC})}/>
                      :
                        <DoubleLeftOutlined  onClick={()=>this.setState({fullPC: !this.state.fullPC})}/>
                       }
                      </div>
                  }
                      {this.state.fullPC != true && 
                      <div style={{width:"5%"}}>
                    {this.state.fullGRN != true ?
                  <DoubleLeftOutlined  onClick={()=>this.setState({fullGRN: !this.state.fullGRN})}/>
                      :
                  <DoubleRightOutlined onClick={()=>this.setState({fullGRN: !this.state.fullGRN})}/>
                    }  
                  </div>
                       }

                    </div>
                {this.state.fullPC != true && 
                <div style={{width:this.state.fullGRN == true ? "100%" : "50%",height:"350px",}}>
                {/* // <Col span={11}> */}
                <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>GRN</h4> 
              
                {/* <Button type="primary" shape="circle" icon={ */}
                  
                {/* <DoubleLeftOutlined style={{alignSelf:"center",textAlign:"center"}} onClick={()=>this.setState({fullGRN: !this.state.fullGRN})}/> */}
              
              {/*  } 
                 style={{marginLeft:"10px", background:"#353b8d", borderColor:"#353b8d"}}
                 onClick={()=>this.setState({fullGRN: !this.state.fullGRN})}
                 />   */}
              
              <div style={{height:"270px",overflowY:"scroll",whiteSpace:"nowrap",}}>
              <Table 
              columns={GRN} 
              bordered={true}
              rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={Grn} 
             pagination={false}
             style={{margin:'1%'}} 
             className='product' 
             />
             </div>
             
             </div>
                 // </Col> 
               }
                {/* <Col span={8}>
                <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Inventory</h4> 
              <Table 
              columns={Inventory} 
              bordered={true}
              rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={inventory} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' 
             />
                </Col> */}
            {/* </Row> */}

            </div>

            <div style={{padding: "5px"}}>
                <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Inventory</h4> 
                <div style={{height:"270px",overflowY:"scroll",whiteSpace:"nowrap",}}> 
              <Table 
              columns={Inventory} 
              bordered={true}
              rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={inventory} 
             pagination={false}
             style={{margin:'1%'}} 
             className='product' 
             />
             </div>
            </div>    

            
            {/* <div style={{flex: "48%",padding: "10px"}}>
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
           
            <div style={{padding: "5px",marginBottom:"5%"}}>
      
              
                  <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"21px",}}>Related Products</h4> 
{/* '#213A87' */}
          <div style={{height:"270px",overflowY:"scroll",whiteSpace:"nowrap"}}> 
              <Table columns={columns1} bordered={true}
              rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={relatedProd} 
             pagination={false}
             style={{margin:'1%'}} 
             className='product' 
             />
            </div>

            </div>

            </div>
              
        


           <Modal
            title="Product Classifications"
            centered
            style={{marginLeft: 20}}
              visible={this.state.clasiModal}
              // onOk={()=>{this.setState({clasiModal: false, prodClasiType: '', prodClasiValue: ''})}}
              // onCancel={()=>{this.setState({clasiModal: false, prodClasiType: '', prodClasiValue: ''})}}
              onOk={this.handleModalOk}
             onCancel={this.handleModalCancel}
              footer={null}
              width={750}
            >
          <Form initialValues={{ type:'',value:'' }}>
           <Row>
        <Col span={11}>
        <Form.Item
        name="ctype"
        label="Type"
        rules={[
          {
            required: true,
            message: 'Please Select Type',
          },
        ]}
      >
       <Select placeholder="Please Select Role" allowClear={true} onChange={(type)=>this.handleClasiType(type)} defaultValue={this.state.prodClasiType}>
        {productClassification.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
       </Select>
      </Form.Item>
      </Col>
        <Col span={11} offset={2}>
       <Form.Item
        name="cvalue"
        label="Value"
        rules={[
          {
            required: true,
            message: 'Please Select Value',
          },
        ]}
      >
       <Select placeholder="Please Select Role" allowClear={true} onChange={(val)=>this.handleClasiValue(val)} defaultValue={this.state.prodClasiValue}>
        {productClassification.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
       </Select>
      </Form.Item>
        </Col>
        </Row>
        
        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                 onClick={()=>{this.addClasi()}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Add
            </Button>
            </div>
            </Form>
          {/* {productClassification.map((c,i) => {
            return (

              // <Option key={c.value} value={c.value}>
          //     <Card
          //     bordered={true}
          //     hoverable
          //     style={{
          //       width: "100%",
          //       marginTop: 5,
          //       height: "65px",
          //       borderRadius: 5,
                
          //       // backgroundColor: this.state.prodClasi.findIndex((p)=>{p == c.value}) != -1 ? '#ececec' : '#fefefe',
          //       backgroundColor: this.state.prodClasi[i] == c.value ? '#ececec' : '#fefefe',
          //     }}
          //     onClick={()=>this.addClassi(c)}
          //   > 
          //  <Row><Col span={12}> <h4 style={{fontWeight:'bold'}}>{c.value}</h4></Col>
          //  <Col span={1} offset={11}> 
          //     {this.state.prodClasi.findIndex((item) => item.prodVal == c.value) != -1 ? <CheckCircleOutlined style={{color:'green',fontSize: 22}}/> : null}
          //  </Col>
          //  </Row>
          //   </Card>
              // </Option>
            );
            })} */}
        </Modal>

        <Modal
        title="Add Comment"
        centered
          visible={this.state.commentModal}
          onOk={()=>{this.setState({commentModal: false})}}
          onCancel={()=>{this.setState({commentModal: false})}}
          footer={null}
          width={600}
        >
          <Form 
      //  onFinish={this.onSubmit} 
       layout="vertical">
       <Row gutter={24}>
       {/* <Col span={3}></Col> */}
       <Col span={22}>
       <Form.Item
        name="ftype"
        label="Comment"
        rules={[
          {
            required: true,
            message: 'Comment is Required',
          },
        ]}
      >
        <TextArea rows={4} />
       
      </Form.Item>
        </Col>
        {/* <Col span={5}></Col> */}
       <Col span={18}>
        <Form.Item
        name="ftype"
        label="Assign to User"
        // initialValue= " "
        rules={[
          {
            // required: true,
            message: 'Please Select user',
          },
        ]}
      >
       <Select placeholder="Please Select user" allowClear={true} onChange={(val)=>this.handleFeatureType(val)} initialValue={this.state.prodFeatureType}>
       {group.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
             {c.label} 
          </Option>
          );
        })}
       </Select>

      </Form.Item>
      </Col>
      {/* <Col span={3}></Col> */}
       <Col span={18}>
      <Form.Item
        name="ftype"
        label="Assign To Supplier"
        // initialValue= " "
        rules={[
          {
            // required: true,
            message: 'Please Select supplier',
          },
        ]}
      >
       <Select placeholder="Please Select supplier" allowClear={true} onChange={(val)=>this.handleFeatureType(val)} initialValue={this.state.prodFeatureType}>
       {/* {productFeatures.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
             {c.label} 
          </Option>
          );
        })}*/}
       </Select>

      </Form.Item>
      </Col>
        </Row>
       
        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                //  onClick={()=>{this.addFeature()}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Submit
            </Button>
            </div>
        </Form>
        </Modal>

        <Modal
        title="Add Tags"
        centered
          visible={this.state.tagModal}
          onOk={()=>{this.setState({tagModal: false})}}
          onCancel={()=>{this.setState({tagModal: false})}}
          footer={null}
          width={600}
        >
          <Form 
      //  onFinish={this.onSubmit} 
       layout="vertical">
       <Row gutter={24}>
       {/* <Col span={3}></Col> */}
       <Col span={18}>
       <Form.Item
        name="ftype"
        label="Select Tags : "
        rules={[
          {
            required: true,
            message: ' Select Tag',
          },
        ]}
      >
      
      <Select  style={{ width: '100%',marginTop:"2%" }} placeholder="Select Tags" mode="multiple"
            // onChange={this.handleChange}
            >
             {tags.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
            </Select> 
      </Form.Item>
        </Col>
        {/* <Col span={5}></Col> */}
       <Col span={18}>
        <Form.Item
        name="ftype"
        label="Select Severity : "
        // initialValue= " "
        rules={[
          {
            // required: true,
            message: 'Please Select severity',
          },
        ]}
      >
       <Select placeholder="Please Select severity" style={{ width: '100%',}} allowClear={true} 
      //  onChange={(val)=>this.handleFeatureType(val)} 
      //  initialValue={this.state.prodFeatureType}
       >
          <Option value="low">Low</Option>
          <Option value="medium">Medium</Option>
          <Option value="high">High</Option>
       </Select>

      </Form.Item>
      </Col>
      {/* <Col span={3}></Col> */}
       <Col span={18}>
      {/* <Form.Item
        name="ftype"
        label="Date of Expiry : "
        // initialValue= " "
        rules={[
          {
            // required: true,
            message: 'Please Select Date',
          },
        ]}
      > */}
      {/* <DatePicker
                        onChange={(date, dateString) => {
                          this.onDateChange(date, dateString, this);
                        }}
                        // showTime
                        format="YYYY-MM-DD"
                        // value={startValue}
                        placeholder="Start"
                        // onOpenChange={this.handleStartOpenChange}
                      /> */}
        {/* <DatePicker
        onChange={(date) => {
          this.onDateChange(date)}}
        // value={value}
      /> */}
      {/* </Form.Item> */}
      </Col>
        </Row>
       
        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                //  onClick={()=>{this.addFeature()}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Submit
            </Button>
            </div>
        </Form>
        </Modal>

        <Modal
        title="Product Features"
        centered
         style={{marginLeft: 40}}
        // style={{marginLeft: 970}}
          visible={this.state.featuresModal}
          // onOk={()=>{this.setState({featuresModal: false, prodFeatureType: '', prodFeatureValue: ''})}}
          onOk={this.handleModalOk}
          onCancel={this.handleModalCancel}
          // onCancel={()=>{this.setState({featuresModal: false, prodFeatureType: '', prodFeatureValue: ''})}}
          footer={null}
          width={750}
        >
          <Form form={form} initialValues={{
            remember: false,
            }}>
           <Row>
        <Col span={11}>
        <Form.Item
        name="ftype"
        label="Type"
        // initialValue= " "
        rules={[
          {
            required: true,
            message: 'Please Select Type',
          },
        ]}
      >
       <Select placeholder="Please Select Type" allowClear={true} onChange={(val)=>this.handleFeatureType(val)} initialValue={this.state.prodFeatureType}>
        {productFeatures.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
       </Select>

      </Form.Item>
      </Col>

        <Col span={11} offset={2}>
       <Form.Item
        name="fvalue"
        label="Value"
        // initialValue=" "
        rules={[
          {
            required: true,
            message: 'Please Enter Value',
          },
        ]}
      >
        <Input onChange={(e)=>this.handleFeatureValue(e.target.value)} allowClear={true} initialValue={this.state.prodFeatureValue}/>
      </Form.Item>
        </Col>
        </Row>
        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                 onClick={()=>{this.addFeature()}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Submit
            </Button>
            </div>
        </Form>
          {/* <div> 
          {productFeatures.map((c) => {
            return (
              <Card
              // key={i}
              bordered={true}
              hoverable
              style={{
                width: "100%",
                marginTop: 5,
                height: "65px",
                borderRadius:5,
                backgroundColor: this.state.selected ? '#ececec' : '#fefefe'
              }}
              onClick={()=>this.addFeature(c)}
            > 
           <Row><Col span={12}> <h4 style={{fontWeight:'bold'}}>{c.value}</h4></Col>
           <Col span={1} offset={11}> 
              {this.state.prodFeature.findIndex((item) => item.prodVal == c.value) != -1 ? <CheckCircleOutlined style={{color:'green',fontSize: 22}}/> : null}
           </Col>
           </Row>
            </Card>
            );
            })}
          </div> */}
        </Modal>

        

        <Modal
        title="Create New Tasks"
        centered
          visible={this.state.taskModal}
          onOk={()=>{this.setState({taskModal: false})}}
          onCancel={()=>{this.setState({taskModal: false})}}
          footer={null}
          width={600}
        >
          <CreateTasks
              // imageArr={this.state.imgArr}
              // exh={this.props.exh}
              // closeModal={this.handleOk}
            />
          
        </Modal>

        <Modal
        title="Purchase Order ACE16"
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

        <Modal
        title="Comments"
        centered
          visible={this.state.commentView}
          onOk={()=>{this.setState({commentView: false})}}
          onCancel={()=>{this.setState({commentView: false})}}
          footer={null}
          width={800}
        >
              <Table 
              columns={viewComment} bordered={true}
              rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={viewComments} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' 
             />
        </Modal>

         </App>
          )
      }else{
        return(
        <Appm>
        {/* {console.log("In Mob : ...",)} */}
        <Tabs defaultActiveKey="1" 
        // onChange={callback}
        >
        {/* <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"17px",marginBottom:"3%"}}>Overview</h4>  */}
        <TabPane tab="Overview" key="1">
             <Card bordered={false} style={{borderRadius: 5 ,marginTop: 5,width:"100%", background: "#eaeaf2"}}> 
           
             <Row >
               <Col span={24}>
                <h4 style={{fontSize: "14px",}}> Product Code : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>ACE16</span></h4>
               </Col>
               </Row>
               <Row>
               <Col span={24}>
                <h4 style={{fontSize: "14px", }}> DT Description : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>ACECLOFENAC 100MG TABLETS</span></h4>   
               </Col>
               </Row>
               <Row>
               <Col span={24}>
               <h4 style={{fontSize: "14px", }}> DT Type : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>Tablet</span></h4>
               </Col>
               </Row>
               <Row>
               <Col span={24}>
               <h4 style={{fontSize: "14px", }}> Status : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>
                 <Tag style={{fontSize: "14px", fontWeight:'bold'}} color="green">Live</Tag> /  &nbsp;<Tag style={{fontSize: "14px", fontWeight:'bold'}} color="red">Discontinued</Tag></span></h4>
               </Col>
               </Row>

               <Divider />

               <div style={{marginTop:"5%"}}>
               <Row gutter={[16, 16]}>
                  <Col span={4}>

                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}>M (Current Month)</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}>M-1</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}>M-2</h4>
                  </Col>
                  <Col span={3}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}>M-3</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}>Average</h4>
                  </Col>
                  </Row>
                  <Row style={{marginTop:"2%"}}>
                  <Col span={4}>
                   <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}> 
                   Price Concession
                   </h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>£ 5.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>£ 7.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>£ 6.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>£ 8.00</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>£ 8.00</h4>
                  </Col>
                </Row>
                <Row>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}>Usage</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>1000</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>1001</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>1002</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>1003</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>1001.5</h4>
                  </Col>
                </Row>
                <Row>
                  <Col span={4}>
                   <h4 style={{fontWeight:"bold",color:"#213A87",fontSize:"14px"}}> PCA </h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>2000</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>2001</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>2002</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>2003</h4>
                  </Col>
                  <Col span={4}>
                  <h4 style={{fontWeight:"bold",fontSize:"14px"}}>2001.5</h4>
                  </Col>
                </Row>
                </div>

               </Card>
              </TabPane>

              {/* <TabPane tab="Classification" key="2">
               <Row style={{marginTop:15}}>

                <Col span={24}>
                <Card bordered={false} style={{borderRadius: 5, marginTop: 5,background: "#eaeaf2"}}>
                <div style={{ float: "right"}}>
                <Tooltip title="Add Classification">
               </Tooltip>
               </div>
               <div style={{marginTop: 5,}}>
                  
                                  {/* {clasData > 0 &&  *
                                  <Row style={{marginBottom: 15,marginLeft: 35}}>
                                     <Col>
                                    
                                    </Col>
                                    <Col offset={13}>
                                    
                                    </Col>
                                    </Row>

                            {clasData &&
                              clasData.map((p,i) => {
                                // console.log("In Map",p)
                                return (
                                  <>
                                
                                  <Row gutter={24} style={{marginBottom: 5,}}>
                                    
                                    <span
                                      style={{
                                        display: "inline-block",
                                        height: "6px",
                                        width: "6px",
                                        background: '#48486c',
                                        // marginRight: "3px",
                                        marginTop: 7,
                                      }}
                                    ></span>
                                    {/* "#001529" *
                                    <Col span={14}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -25,fontWeight: 'bold',
                                      marginLeft: 2,fontSize: "14px"
                                      }}
                                    >
                                      {p.value}
                                    </span>
                                    </Col>
                                    <Col span={8}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -35,fontWeight: 'bold',
                                      marginLeft: 5,fontSize: "14px"
                                      }}
                                    >
                                      {p.name}
                                    </span>
                                    </Col>
                                 </Row>
                                 </>
                                );
                              })}

                            {!this.state.prodClasi && (
                              <Row style={{ marginBottom: "10px" }}>
                                <p>No products available</p>
                              </Row>
                            )}
                    </div>
                </Card>
                </Col>
                </Row>
                </TabPane> */}

                {/* <TabPane tab="Features" key="3">
                <Row style={{marginTop:15}}>
                <Col span={24}>
                <Card bordered={false} style={{borderRadius: 5,marginTop: 5,background: "#eaeaf2"}}> 
                <div style={{ float: "right"}}>
                <Tooltip title="Add Features">
               </Tooltip>
               </div>
               <div style={{marginTop: 5}}>
               <Row style={{marginBottom: 15,marginLeft: 35}}>
                                     <Col>
                                    </Col>
                                    <Col offset={11}>
                                    </Col>
                                    
                                    </Row>
                            {featureData &&
                              featureData.map((p,i) => {
                                return (
                                  <Row gutter={24} style={{marginBottom: 5}}>
                                    <span
                                      style={{
                                        display: "inline-block",
                                        height: "6px",
                                        width: "6px",
                                        background: '#48486c', //#a9dbfc
                                        marginRight: "6px",
                                        marginTop: 7,
                                      }}
                                    ></span>
                                     <Col span={13}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -25,fontWeight: 'bold',fontSize: "14px"
                                      }}
                                    >
                                      {p.name}
                                    </span>
                                    </Col>
                                    <Col span={8}>
                                    <span
                                      style={{cursor: "pointer",marginTop: -35,fontWeight: 'bold',fontSize: "14px"
                                      }}
                                    >
                                      {p.value}
                                    </span>
                                    </Col>
                                 </Row>
                                );
                              })}

                    </div>
                    
                </Card>
                </Col>
               </Row>
               </TabPane> */}

               <TabPane tab="Notes & Tags" key="4">
               <Card bordered={false} 
                  style={{borderRadius: 5,marginTop:"4%",background: "#eaeaf2"}}
               >
              <Row>
              <h4 style={{color:'#213a87', float: "left", fontWeight:'bold',fontSize: "17px",marginBottom:"1%"}}>Notes</h4>
              </Row>
              <Row>
              <h4 style={{float: "left", fontSize: "14px", margin: 5,}}><span style={{fontWeight:"bold"}}>M013</span> is giving best price for ACAT19.</h4>
              
              <h4 style={{float: "left", fontSize: "14px", margin: 5,}}></h4>
            </Row>
            <Row>  
            <h4 style={{float: "left", fontSize: "14px", margin: 5,}}><span style={{fontWeight:"bold"}}>ACAT16</span> had been added to short-dated.</h4>
            </Row>
            <Row>  
            <h4 style={{float: "left", fontSize: "14px", margin: 5,}}><span style={{fontWeight:"bold"}}>ACAT16</span> has been added in offer.</h4>
            </Row>
            <Row style={{marginTop:"7%"}}>
              <Col span={20}>
              <Button type="link" 
                 onClick={()=>{this.setState({commentView: true})}}
                style={{color:'#213a87',fontWeight:"bold",fontSize:"14px", borderRadius:5}}>
                  View More
            </Button>
              </Col>
              <Col>
              <PlusCircleOutlined style={{fontSize: "20px",color:'#213a87'}} onClick={()=>{this.setState({commentModal: true})}}/>
            </Col> 
            </Row>
            <br/>
            <Divider/>
            <br/>
            
            <Row>
              <h4 style={{color:'#213a87', float: "left", fontWeight:'bold',fontSize: "17px",marginBottom:"1%"}}>Attached Tags</h4>
              </Row>
              <Row>
                <Row>
                  <Col span={4}>
                    <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="orange">In Offer</Tag>
                  </Col>
                  <Col span={4}>
                    <Tag style={{fontSize: "17px", fontWeight:'bold'}} color="red">Out Of Stock</Tag>
                  </Col>
                </Row>
              </Row>
            <Row>
            <Select  style={{ width: '100%',marginTop:"2%" }} placeholder="Select Tags" mode="multiple"
            // onChange={this.handleChange}
            >
             {tags.map((c) => {
          return (
          <Option key={c.value} value={c.value}>
            {c.label}
          </Option>
          );
        })}
            </Select>
            <Button type="primary" 
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,borderRadius:5}}>
                  Attach Tags
            </Button>
            </Row>
            </Card>       
               </TabPane>
               </Tabs>
               <Tabs>
                 <TabPane tab="Product Capture" key="11">
                 
                 <Row style={{marginTop:"20px",height:'300px', overflow:'auto'}}>
                                    <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {productsCapta.length > 0 ? 
                                        
                                        productsCapta 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                  </Row>

                 </TabPane>
                 <TabPane tab="GRN" key="12">
                 <Row style={{marginTop:"20px",height:'300px', overflow:'auto'}}>
                                    <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {productsGrn.length > 0 ? 
                                        
                                        productsGrn 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                  </Row>

                 </TabPane>
                 <TabPane tab="Inventory" key="13">
                 <Row style={{marginTop:"20px",height:'300px', overflow:'auto'}}>
                                    <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {productsInventory.length > 0 ? 
                                        
                                        productsInventory 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                  </Row>
                 </TabPane>

                 <TabPane tab="Related Poducts" key="14">
                      
                      <Row style={{marginTop:"20px",height:'300px', overflow:'auto'}}>
                                      <InfiniteScroll
                                        initialLoad={false}
                                        // loadMore={this.handleInfiniteScroll}
                                        // hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {rproducts.length > 0 ? 
                                        
                                        rproducts 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
                      </Row>


                 </TabPane>
               </Tabs>
               <Modal
        title="Add Comment"
        centered
          visible={this.state.commentModal}
          onOk={()=>{this.setState({commentModal: false})}}
          onCancel={()=>{this.setState({commentModal: false})}}
          footer={null}
          width={600}
        >
          <Form 
      //  onFinish={this.onSubmit} 
       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="ftype"
        label="Comment"
        rules={[
          {
            required: true,
            message: 'Comment is Required',
          },
        ]}
      >
        <TextArea rows={4} />
       
      </Form.Item>
        </Col>
        </Row>
       
        <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                //  onClick={()=>{this.addFeature()}}
                style={{backgroundColor:'#353b8d', borderColor:'#353b8d', margin: 5, marginTop: 35,}}>
                  Submit
            </Button>
            </div>
        </Form>
        </Modal>

        <Modal
        title="Comments"
        centered
          visible={this.state.commentView}
          onOk={()=>{this.setState({commentView: false})}}
          onCancel={()=>{this.setState({commentView: false})}}
          footer={null}
          width={800}
        >
              <Table 
              columns={viewComment} bordered={true}
              rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            //  expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={viewComments} 
             pagination={false}
             style={{margin:'2%'}} 
             className='product' 
             />
        </Modal>
        </Appm>
        )
      }
      
    }
    }
    
    </MediaQuery>
     )   
    }

}

export default Products