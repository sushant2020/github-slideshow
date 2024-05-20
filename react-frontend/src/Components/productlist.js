import React, {Component} from "react";
import {Form, 
  Button, 
  Row, 
  Col, 
  Input, 
  Select, 
  Modal, 
  Divider, 
  Table, 
  Tag, 
  Popconfirm, 
  Space, 
  Spin,
  message, 
  Radio, 
  Card,
} from "antd";
// import { Drawer, List, NavBar, Button as MButton,Accordion, Icon as MIcon } from 'antd-mobile';
import App from "../App";
// import { withApollo } from "react-apollo";
import CreateProduct from "./createProduct"
import axios from 'axios';
import moment from 'moment'
import MediaQuery from 'react-responsive';
import Appm from "../mApp"
import InfiniteScroll from 'react-infinite-scroller';
import { List, Button as MButton,Flex, WhiteSpace,NavBar } from 'antd-mobile';

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    MinusCircleOutlined, 
    PlusOutlined,
    EditFilled,
    HighlightFilled,
    DeleteFilled,
    SortAscendingOutlined
  
  } from '@ant-design/icons';

  const { Option } = Select;
  const { Search } = Input;
  
  const data = [
    {
      key: 'ACAT31',
      type: 'Tablet',
      desc: 'Acamprosate 333mg gastro-resistant tablets',
      price: '38.25',
      date: '09-06-2021',
    },
    {
      key: 'ZCAT19',
      type: 'Tablet',
      desc: 'Acarbose 100mg tablets',
      price: '25.29',
      date: '09-06-2021',
    },
    {
      key: 'DCAT59',
      type: 'Tablet',
      desc: 'Acarbose 50mg tablets',
      price: '14.58',
      date: '09-06-2021',
      },
    {
      key: 'PCET66',
      type: 'capsule',
      desc: 'Acetylcysteine 600mg capsules',
      price: '47.59',
      date: '09-06-2021',
    },

  ];
  
 

class AddProduct extends Component {
    constructor(props) {
        super(props);
        this.state = {
        form: false,
        collapsed: false,
        tableData: [],
        action: '',
        userModal: false,
        viewModal: false,
        value: null,
        prodData: [],
        prodloading: false,
        };
      }

    componentDidMount(){
        // console.log("In CDM");
        this.setState({
          prodloading: true
        })
        let getURL= 'https://api.sigmaproductmaster.webdezign.uk/api/products'
        // const resp = axios.get(`${Api.getProducts}`);
        axios.get(getURL).then((response) => {
          // console.log("Response...::",response.data)
          if(response){
          this.setState({
            prodData: response.data,
            prodloading: false
          })
        }else{
          this.setState({
            // prodData: response.data,
            prodloading: false
          })
        }
        })
    }

    onSubmit=()=>{

      this.setState({userModal: false, viewModal: false})
    //   message.success('Classification created Successfully');
    }
    
    
    onFinish = values => {
        console.log('Received values of form:', values);
        this.setState({userModal: false, viewModal: false})
      };

    onSearch=(e)=>{
      
      let n = e.target.value;
      // console.log("In Search",n)
      // console.log("In Search",this.state.prodData)
      this.setState({
        prodloading: true
      })
       // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
       // const found = data.find(element => element.name == n);
      // return
       let arr = [];
       this.state.prodData.map((i, j) => {
         if (
                i.product_code.toLowerCase().includes(n.toLowerCase()) 
            ||  i.parent_product_code.toLowerCase().includes(n.toLowerCase())
             || i.clean_description.toLowerCase().includes(n.toLowerCase()) 
            //  || i.dt_type.toLowerCase().includes(n)
            //  || i.dt_pack.includes(n) 
            //  || i.dt_price.includes(n)  
            //  || i.key.includes(n) 
             ) {
           arr.push(i);
         }
       });
       
       // console.log("Search Index:: ",index)
      // console.log("Search Name:: ",arr)
       this.setState({
         tableData: arr,
         prodloading: false
       })
       if(n == ''){
        // console.log("Search No Result:: ")
         
         this.setState({
           tableData: this.state.prodData,
           prodloading: false
         })
       }
     }

     onChange = (e) => {
      //console.log("radio checked", e.target.value);
      this.setState({
        value: e.target.value,
      });
    };

    nameSort=()=>{
      console.log("Data in sort:: ");
      const sortdata = this.state.prodData;
      console.log("Bef Sort",data);

      // let data1 = data.forEach((p)=> p.dt_type && p.dt_type.sort())
      // console.log("Aft Sort::",data1)
    }

    render(){ 

      const formItemLayout = {
        labelCol: {
          xs: { span: 24 },
          sm: { span: 4 },
        },
        wrapperCol: {
          xs: { span: 24 },
          sm: { span: 20 },
        },
      };
      const formItemLayoutWithOutLabel = {
        wrapperCol: {
          xs: { span: 24, offset: 0 },
          sm: { span: 20, offset: 4 },
        },
      };
      const { value } = this.state;


  const columns = [
     {
       title: 'Parent Code',
          // title: () =>{return(
          //   <div>Parent Code <SortAscendingOutlined onClick={()=>{this.nameSort()}} style={{color:"#abb2b9"}}/></div>
          // )},
          // sorter: (a, b) => a.parent_product_code - b.parent_product_code,
        //   width: 200,
          render:(data)=>{
            return(
              <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.parent_product_code}</h3>
            )
          }
          //dataIndex: 'key',
    },
    {
      title: 'Product Code',
      // sorter: (a, b) => a.product_code - b.product_code,
    //   width: 200,
      render:(data)=>{
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.product_code}</h3>
        )
      }
      //dataIndex: 'key',
    },
    {
      title: 'DT Desc',
      // dataIndex: 'name',
      // sorter: (a, b) => a.clean_description - b.clean_description,
      key: 'dtDesc',
      width: "30%",
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.clean_description}</h3>
        )
      }
    },
    {
      title: 'DT Pack',
      // dataIndex: 'name',
      key: 'dtDesc',
      sorter: (a, b) => a.dt_pack - b.dt_pack,
    //   width: 450,
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.dt_pack}</h3>
        )
      }
    },
    {
      title: 'DT Type',
      sort: (a) => a.dt_type,
      // dataIndex: 'name',
      key: 'dtType',
      // width: "30%",
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.dt_type}</h3>
        )
      }
    },
    {
        title: 'Price',
        // dataIndex: 'name',
        key: 'price',
        sorter: (a, b) => a.dt_price - b.dt_price,
      //   width: 450,
        render:(data)=>{
          // console.log("Data..",data.dt_price.toFixed(2))
          // let n = data.dt_price.toFixed(2)
          return(
            <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.dt_price}</h3>
          )
        }
      },
    // {
    //     title: 'Created At',
    //     // dataIndex: 'name',
    //     key: 'createdAt',
    //     // sorter: (a, b) => a.date - b.date,
    //     // width: 450,
    //     render:(data)=>{
    //       // console.log("Data..",data)
    //       return(
    //         <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{moment(data.created_at).format('DD/MM/YYYY')}</h3>
    //       )
    //     }
    //   },
    // {
    //   title: 'Action',
    //   key: 'action',
    //   render: (text, record) => (
    //     // <>
    //      <Space size="middle"> 
    //       {/* <Button type="primary" size="large" icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}} 
    //       onClick={()=>this.setState({viewModal: true,action: 'view'})} 
    //       />  */}
    //       <EditFilled 
    //       onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
    //       style={{fontSize:"25px"}}/>
    //       {/* <Button type="primary" icon={<EditOutlined style={{fontSize:"22px"}}/>} style={{marginLeft:"10px", background:"#353b8d", borderColor:"#353b8d"}}
    //       onClick={()=>this.setState({viewModal: true,action: 'edit'})}
    //       />  */}
    //       &nbsp;<Popconfirm title="Sure to delete?" 
    //       //onConfirm={() => this.handleDelete(data.prodId)}
    //       >
    //       <DeleteFilled 
    //             // onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} 
    //             style={{fontSize:"25px",color:"#D10000"}}/>
    //       </Popconfirm> 
    //       {/* <DeleteOutlined /> */}
    //      </Space> 
    //     // </>
    //   ),
    // },
  ];
  const columnsMob = [
    {
      // title: 'Parent Code',
         title: () =>{return(
           <div style={{fontSize:"13px",color:"#213A87",fontWeight:"bold"}}> Parent Code </div>
         )},
        //  sorter: (a, b) => a.parent_product_code - b.parent_product_code,
       //   width: 200,
         render:(data)=>{
           return(
             <h3 style={{fontWeight:'bold',fontSize:"12px"}}>{data.parent_product_code}</h3>
           )
         }
         //dataIndex: 'key',
   },
   {
    //  title: 'Product Code',
     // sorter: (a, b) => a.product_code - b.product_code,
   //   width: 200,
   title: () =>{return(
    <div style={{fontSize:"13px",color:"#213A87",fontWeight:"bold"}}> Product Code </div>
  )},
     render:(data)=>{
       return(
         <h3 style={{fontWeight:'bold',fontSize:"12px"}}>{data.product_code}</h3>
       )
     }
     //dataIndex: 'key',
   },
   {
    //  title: 'DT Desc',
    title: () =>{return(
      <div style={{fontSize:"13px",color:"#213A87",fontWeight:"bold"}}> DT Desc </div>
    )},
     // dataIndex: 'name',
     // sorter: (a, b) => a.clean_description - b.clean_description,
     key: 'dtDesc',
     width: "30%",
     render:(data)=>{
       // console.log("Data..",data)
       return(
         <h3 style={{fontWeight:'bold',fontSize:"12px" }}>{data.clean_description}</h3>
       )
     }
   },
   {
    //  title: 'DT Pack',
    title: () =>{return(
      <div style={{fontSize:"13px",color:"#213A87",fontWeight:"bold"}}> DT Pack </div>
    )},
     // dataIndex: 'name',
     key: 'dtDesc',
     sorter: (a, b) => a.dt_pack - b.dt_pack,
   //   width: 450,
     render:(data)=>{
       // console.log("Data..",data)
       return(
         <h3 style={{fontWeight:'bold',fontSize:"12px" }}>{data.dt_pack}</h3>
       )
     }
   },
   {
    //  title: 'DT Type',
    title: () =>{return(
      <div style={{fontSize:"13px",color:"#213A87",fontWeight:"bold"}}> DT Type </div>
    )},
     sort: (a) => a.dt_type,
     // dataIndex: 'name',
     key: 'dtType',
     // width: "30%",
     render:(data)=>{
       // console.log("Data..",data)
       return(
         <h3 style={{fontWeight:'bold',fontSize:"12px" }}>{data.dt_type}</h3>
       )
     }
   },
   {
      //  title: 'Price',
      title: () =>{return(
        <div style={{fontSize:"13px",color:"#213A87",fontWeight:"bold"}}> Price </div>
      )},
       // dataIndex: 'name',
       key: 'price',
       sorter: (a, b) => a.dt_price - b.dt_price,
     //   width: 450,
       render:(data)=>{
         // console.log("Data..",data.dt_price.toFixed(2))
         // let n = data.dt_price.toFixed(2)
         return(
           <h3 style={{fontWeight:'bold',fontSize:"12px" }}>{data.dt_price}</h3>
         )
       }
     },
 ];
  // const columns = [
  // //   {
  // //        title: 'Parent Code',
  // //      //   width: 200,
  // //        render:(data)=>{
  // //          return(
  // //            <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.parent_product_code}</h3>
  // //          )
  // //        }
  // //        //dataIndex: 'key',
  // //  },
  //  {
  //    title: 'Product Code',
  //  //   width: 200,
  //    render:(data)=>{
  //      return(
  //        <h3 style={{fontWeight:'bold',fontSize:"15px"}}>{data.key}</h3>
  //      )
  //    }
  //    //dataIndex: 'key',
  //  },
  //  {
  //    title: 'DT Desc',
  //    // dataIndex: 'name',
  //    key: 'dtDesc',
  //  //   width: 450,
  //    render:(data)=>{
  //      // console.log("Data..",data)
  //      return(
  //        <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.desc}</h3>
  //      )
  //    }
  //  },
  //  {
  //    title: 'DT Pack',
  //    // dataIndex: 'name',
  //    key: 'dtDesc',
  //   //  sorter: (a, b) => a.dt_pack - b.dt_pack,
  //  //   width: 450,
  //    render:(data)=>{
  //      // console.log("Data..",data)
  //      return(
  //        <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.type}</h3>
  //      )
  //    }
  //  },
  //  {
  //    title: 'DT Type',
  //    // dataIndex: 'name',
  //    key: 'dtType',
  //  //   width: 450,
  //    render:(data)=>{
  //      // console.log("Data..",data)
  //      return(
  //        <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.type}</h3>
  //      )
  //    }
  //  },
  //  {
  //      title: 'Price',
  //      // dataIndex: 'name',
  //      key: 'price',
  //      sorter: (a, b) => a.dt_price - b.dt_price,
  //    //   width: 450,
  //      render:(data)=>{
  //        // console.log("Data..",data.dt_price.toFixed(2))
  //        // let n = data.dt_price.toFixed(2)
  //        return(
  //          <h3 style={{fontWeight:'bold',fontSize:"15px" }}>{data.price}</h3>
  //        )
  //      }
  //    },
  // ]

  let products = this.state.prodData.map((p)=>{
    // console.log("record..",p)
      return(
          <List.Item style={{marginBottom:'5px'}}>
            <Card style={{background:"#dfdfee",width:"100%",padding:"10px"}}>
           <div> <h4>Parent Code : </h4>
              <p style={{fontWeight:"bold"}}>
                {p.parent_product_code}
             </p>
             </div>
             <div> <h4>Product Code : </h4>
              <p style={{fontWeight:"bold"}}>
              {p.product_code}
             </p>
             </div>
             <div> <h4>DT Description : </h4>
              <p style={{fontWeight:"bold"}}>
              {p.clean_description}
             </p>
             </div>
             <div> <h4>DT Pack : </h4>
              <p style={{fontWeight:"bold"}}> 
              {p.dt_pack}
             </p>
             </div>
             <div> <h4>DT Type : </h4>
              <p style={{fontWeight:"bold"}}>
              {p.dt_type}
             </p>
             </div>
             <div> <h4>Price : </h4>
              <p style={{fontWeight:"bold"}}>
              {p.dt_price}
             </p>
             </div>
             </Card>
          </List.Item>
      )
  })
        // console.log("In Render");
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
              <div>
      <App header={
        <Input placeholder="Search in Products" size="large"
        allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        // onSearch={(val)=>this.onSearch(val)}
        onChange={(val)=>this.onSearch(val)}
        />
      // <Search placeholder="Search Product Code / Type / Desc / Price in Products" size="large"
      // allowClear style={{width:'30%',marginLeft:"90px",marginTop:"0.7%"}} onSearch={(val)=>this.onSearch(val)}/>
      }>
       <Spin spinning={this.state.prodloading}>
              <div>
                {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Add New</h4> */}
             <Button  type="primary" onClick={()=>{this.setState({userModal: true})}}
                style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right",borderRadius:5 , margin: "10px",boxShadow:'0 0 10px'}}>
                 Add Product
            </Button>
            </div>


            <Table columns={columns} 
            rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            // expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Type</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Value</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
            dataSource={this.state.tableData.length > 0 ? this.state.tableData : this.state.prodData} style={{margin:'2%'}} className='product' onHeaderRow={(columns, index) => {
            return {
            onClick: () => {}, // click header row
             };
            }}         
            pagination={true}
           />

       <Modal
          title="New Product"
          centered
          visible={this.state.userModal}
          onOk={()=>{this.setState({userModal: false})}}
          onCancel={()=>{this.setState({userModal: false})}}
          footer={null}
          width={600}
         >
          <CreateProduct
              // imageArr={this.state.imgArr}
              // exh={this.props.exh}
              // closeModal={this.handleOk}
            />
        </Modal>

        <Modal
                title= {"Edit Product"}
                centered
                visible={this.state.viewModal}
                onOk={()=>{this.setState({viewModal: false})}}
                onCancel={()=>{this.setState({viewModal: false})}}
                footer={null}
                width={600}
                >
        {/* {this.state.action == 'view' && 
        <div>
                  <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Name : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        John Brown
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Email : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        john.brown@gmail.com
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
       <Col span={8} style={{fontSize:20, fontWeight:'bold'}}> Role : </Col>
       <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        Administrator
        </Col>
        </Row>
        <Row gutter={24} style={{padding:10}}>
        <Col span={8} style={{fontSize:20, fontWeight:'bold',}}> Status : </Col>
        <Col span={12} style={{fontSize:20, fontWeight:'bold', color:'#8f0021'}}>
        ACtive
        </Col>
        </Row>
        </div>
       } */}

        {/* {this.state.action == 'edit' &&            */}
            <Form 
    //    onFinish={this.onFinish} 
    // key: 'ACAT31',
    //   type: 'Tablet',
    //   desc: 'Acamprosate 333mg gastro-resistant tablets',
    //   price: '38.25',

       layout="vertical">
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="fname"
        label="Prod Code"
        rules={[
          {
            required: true,
            message: 'Please Enter Code',
          },
        ]}
      >
        <Input disabled={true} defaultValue="ACAT31" style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="lname"
        label="DT Type"
        rules={[
          {
            required: true,
            message: 'Please Type',
          },
        ]}
      >
        <Input disabled={true} defaultValue="Tablet" style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="DT Desc"
        rules={[
          {
            required: true,
            message: 'Please Enter Desc',
          },
        ]}
      >
        <Input defaultValue="Acamprosate 333mg gastro-resistant tablets" style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="Price"
        rules={[
          {
            required: true,
            message: 'Please Enter price',
          },
        ]}
      >
        <Input defaultValue="38.25" style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="Classification"
        rules={[
          {
            required: true,
            message: 'Please Enter price',
          },
        ]}
      >
        <Select style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>

        <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="email"
        label="Feature"
        rules={[
          {
            required: true,
            message: 'Please Enter price',
          },
        ]}
      >
        <Select style={{width:'100%'}}/>
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
        {/* } */}
        </Modal>
        </Spin>
         </App>
         </div>
          
        )}else{
          return(
          <div>
          <Appm pageName={<div>
          <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Test</h4>
        <Input placeholder="Search in Products" size="large"
        allowClear style={{width:'100%',borderRadius:10}} 
        // onChange={(val)=>this.setState({searchVal: val})}
        // onSearch={(val)=>this.onSearch(val)}
        onChange={(val)=>this.onSearch(val)}
        />
        </div>
        }>
          {/* <NavBar
          mode="light"
          leftContent={[
            <div>
              <h4 style={{color:'#48486c', fontWeight:'bold',fontSize:"19px",marginTop:"10%"}}>Test</h4>
            </div>
          ]}
        /> */}
            <div >
          <Table columns={columnsMob}  
            rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
            // expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Type</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Value</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
            dataSource={this.state.tableData.length > 0 ? this.state.tableData : this.state.prodData} className='product' onHeaderRow={(columns, index) => {
            return {
            onClick: () => {}, // click header row
             };
            }}         
            pagination={true}
           />
           </div>
          {/* <Row gutter={20} style={{marginTop:"20px",height:'600px',width: '100%', overflow:'auto'}}>
            
                                    <InfiniteScroll
                                        initialLoad={false}
                                        loadMore={this.handleInfiniteScroll}
                                        hasMore={!this.state.loading && this.state.hasMore}
                                        useWindow={false}
                                        // getScrollParent={() => this.scrollParentRef}
                                    >
                                    {products.length > 0 ? 
                                        
                                        products 
                                        
                                    : "No products available"}
                                    </InfiniteScroll>
            </Row> */}
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

export default AddProduct