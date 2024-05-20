import React, {Component} from "react";
import {Form, Button, Row, Col, Input, Select, Modal, Divider, Table, Tag, Popconfirm ,Space,Card } from "antd";
import App from "../App";
import Appm from "../mApp"
import MediaQuery from 'react-responsive';
import { List, Flex, WhiteSpace } from 'antd-mobile';
import InfiniteScroll from 'react-infinite-scroller';
// import { withApollo } from "react-apollo";

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined,
    SearchOutlined,
    EuroOutlined,
    CloseCircleFilled,
    CheckCircleFilled,
    CheckOutlined,
  } from '@ant-design/icons';

  const { Option } = Select;
  const { Search } = Input;

  const role = [
    { value: "users_manage", label: "users_manage" },
    { value: "manageManualImport", label: "manageManualImport" },
    { value: "manageProduct", label: "manageProduct" },
    { value: "manageAccess", label: "manageAccess" },
    { value: "manageSupplier", label: "manageSupplier" },
    { value: "managePriceData", label: "managePriceData" },
    { value: "manageVolumeData", label: "manageVolumeData" },
    { value: "manageFileImport", label: "manageFileImport" },
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
  
  

class CreatePO extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           productName: '',
        collapsed: false,
        tableData: [],
        searchText: '',
        searchedColumn: '',
        filteredInfo: null,
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

    // getColumnSearchProps = dataIndex => ({
    //   filterDropdown: ({ setSelectedKeys, selectedKeys, confirm, clearFilters }) => (
    //     <div style={{ padding: 8 }}>
    //       <Input
    //         ref={node => {
    //           this.searchInput = node;
    //         }}
    //         placeholder={`Search ${dataIndex}`}
    //         value={selectedKeys[0]}
    //         onChange={e => setSelectedKeys(e.target.value ? [e.target.value] : [])}
    //         onPressEnter={() => this.handleSearch(selectedKeys, confirm, dataIndex)}
    //         style={{ width: 188, marginBottom: 8, display: 'block' }}
    //       />
    //       <Space>
    //         <Button
    //           type="primary"
    //           onClick={() => this.handleSearch(selectedKeys, confirm, dataIndex)}
    //           icon={<SearchOutlined />}
    //           size="small"
    //           style={{ width: 90 }}
    //         >
    //           Search
    //         </Button>
    //         <Button onClick={() => this.handleReset(clearFilters)} size="small" style={{ width: 90 }}>
    //           Reset
    //         </Button>
    //         {/* <Button
    //           type="link"
    //           size="small"
    //           onClick={() => {
    //             confirm({ closeDropdown: false });
    //             this.setState({
    //               searchText: selectedKeys[0],
    //               searchedColumn: dataIndex,
    //             });
    //           }}
    //         >
    //           Filter
    //         </Button> */}
    //       </Space>
    //     </div>
    //   ),
    //   filterIcon: filtered => <SearchOutlined style={{ color: filtered ? '#1890ff' : undefined }} />,
    //   onFilter: (value, record) =>
    //     record[dataIndex]
    //       ? record[dataIndex].toString().toLowerCase().includes(value.toLowerCase())
    //       : '',
    //   onFilterDropdownVisibleChange: visible => {
    //     if (visible) {
    //       setTimeout(() => this.searchInput.select(), 100);
    //     }
    //   },
    // });

    // handleSearch = (selectedKeys, confirm, dataIndex) => {
    //   confirm();
    //   this.setState({
    //     searchText: selectedKeys[0],
    //     searchedColumn: dataIndex,
    //   });
    // };
  
    handleReset = clearFilters => {
      clearFilters();
      this.setState({ searchText: '' });
    };

    onSearch=(e)=>{
      // console.log("In Search",e.target.value)
      let n = e.target.value;
      // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
      // const found = data.find(element => element.name == n);
      let arr = [];
      data.map((i, j) => {
        if (i.name.toLowerCase().includes(n.toLowerCase()) 
        || i.key.toLowerCase().includes(n.toLowerCase()) 
        // || i.qty.includes(n) 
        // || i.price.includes(n) 
        // || i.note.toLowerCase().includes(n.toLowerCase())
        ) {
          arr.push(i);
        }
      });
      
      this.setState({
        tableData: arr
      })
      if(n == ''){
       // console.log("Search No Result:: ")
        
        this.setState({
          tableData: data
        })
      }
    }

    onProducts=()=>{
      this.props.history.push('/products')
     }


    render(){
      
      let { sortedInfo, filteredInfo } = this.state;
    sortedInfo = sortedInfo || {};
    filteredInfo = filteredInfo || {};

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
      title: 'Supplier',
      // ...this.getColumnSearchProps('name'),
      // dataIndex: 'name',
      key: 'name',
      render:(data)=>{
        // console.log("Data..",data)
        return(
          <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.name}</h3>
        )
        
      }
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
      {
        title: 'Created By',
        // ...this.getColumnSearchProps('name'),
        // dataIndex: 'name',
        key: 'by',
        render:(data)=>{
          // console.log("Data..",data)
          return(
            <h3 style={{fontWeight:'bold', fontSize: "15px" }}>{data.user}</h3>
          )
          
        }
      },
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
      // {
      //   title: 'Status',
      //   // dataIndex: 'name',
      //   key: 'note',
      //   // filters: [
      //   //   { text: 'Pending', value: 'Pending' },
      //   //   { text: 'Approved', value: 'Approved' },
      //   // ],
      //   // filteredValue: filteredInfo.name || null,
      //   // onFilter: (value, data) => data.status.includes(value),
      //   render:(data)=>{
      //     // console.log("Data..",data)
      //     return(
      //       <Tag style={{fontWeight:'bold',fontSize:"16px", color:"grey"}}>{data.status}</Tag>
      //     )
      //   }
      // },
    // {
    //   title: 'Action',
    //   key: 'action',
    //   render: (text, record) => (
    //     // <>
    //      <Space size="middle"> 
    //      <Popconfirm title="Sure to Confirm PO" 
    //       //onConfirm={() => this.handleDelete(data.prodId)}
    //       >
    //         <CheckCircleFilled style={{fontSize:"25px", color:"green"}}/>
    //         </Popconfirm> 

    //      <Popconfirm title="Sure to Cancel PO?" 
    //       //onConfirm={() => this.handleDelete(data.prodId)}
    //       >
    //         <CloseCircleFilled style={{fontSize:"25px", color:"red"}}/>{/* <Button type="primary" size="large" style={{background:"#353b8d", borderColor:"#353b8d"}} icon={<CloseOutlined style={{fontSize:25}}/>} />  */}
    //       </Popconfirm>
         
          
    //      </Space> 
    //     // </>
    //   ),
    // },
  ];

  let products = data.map((p)=>{
    // console.log("record..",p)
      return(
          <List.Item style={{marginBottom:'5px'}}>
            <Card bordered={false} style={{borderRadius: 5 ,marginTop: 2,width:"98%", background: "#eaeaf2"}}> 
       
       <Row >
         <Col span={24}>
          <h4 style={{fontSize: "14px",}}> PO-ID : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.key}</span></h4>
         </Col>
         </Row>
         <Row>
         <Col span={24}>
          <h4 style={{fontSize: "14px", }}> Supplier : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.name}</span></h4>   
         </Col>
         </Row>
         <Row>
         <Col span={24}>
         <h4 style={{fontSize: "14px", }}> Quantity : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.qty}</span></h4>
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
         <h4 style={{fontSize: "14px", }}> Created At : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>-</span>
           </h4>
         </Col>
         </Row>
         <Row>
         <Col span={24}>
         <h4 style={{fontSize: "14px", }}> Created By : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>-</span></h4>
         </Col>
         </Row>
         <Row>
         <Col span={24}>
         <h4 style={{fontSize: "14px", }}> Note : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>{p.note}</span>
           </h4>
         </Col>
         </Row>
         <Row>
         <Col span={24}>
         <h4 style={{fontSize: "14px", }}> Status : &nbsp; <span style={{fontSize: "14px", fontWeight:'bold'}}>
         <Tag style={{fontWeight:'bold',fontSize:"14px", color:"grey"}}>
           {p.status}
          </Tag>
           </span>
           </h4>
         </Col>
         </Row>
         </Card>
              <WhiteSpace size="sm" />
          </List.Item>
      )
  })
        // console.log("In Render",this.state.tableData.length);
     return(
      <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
        <App header={
          <Input placeholder="Search by PO-Id / Supplier Name in Purchase Order" size="large"
          allowClear style={{width:'26%',marginLeft:"90px",marginTop:"0.7%",borderRadius:10}} 
          // onChange={(val)=>this.setState({searchVal: val})}
          // onSearch={(val)=>this.onSearch(val)}
          onChange={(val)=>this.onSearch(val)}
          />
        }>
        {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
            Role 
          </Divider> */}
           <div>
           {this.state.productName ?
            <h4 
             style={{fontSize:"14px",color:'#213A87',float:"left",marginTop:"1%",marginLeft:"2%",cursor:"pointer"}} onClick={()=> {this.onProducts()}}>
              go to product <span style={{fontSize:"18px",fontWeight:"bold",color:'#213A87',cursor:"pointer"}}>
                            {this.state.productName ? this.state.productName : null}
                           </span>
             </h4>
             : null}
             {/* <h4 style={{float: "left", fontWeight:'bold', fontSize: "25px", margin: 5, color:"#213A87"}}>Purchase Orders</h4> */}
             
          <Button type="primary" 
          // onClick={()=>{this.setState({userModal: true})}}
             style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius:5}}>
              View All PO
         </Button>
         <Button type="primary" 
          // onClick={()=>{this.setState({userModal: true})}}
             style={{backgroundColor:'#213A87', borderColor:'#213A87', float: "right", margin: "10px",boxShadow:'0 0 10px',borderRadius:5}}>
              Export to CSV
         </Button>
         </div>

         <Divider style={{fontWeight: "bold",}} />

         {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

         <Table columns={columns} 
         rowClassName={(record, index) => index % 2 === 0 ? "table-row-light" : "table-row-dark"}
        //  expandable={{expandedRowRender: record => <p style={{ margin: 0 }}>{record.name}</p>,rowExpandable: record => record.name !== 'Not Expandable',}} 
         dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} style={{margin:'2%'}} className='product' onHeaderRow={(columns, index) => {
        return {onClick: () => {}, // click header row
 };
}}/>

         <Modal
             title="Create New Role"
             centered
             visible={this.state.userModal}
             onOk={()=>{this.setState({userModal: false})}}
             onCancel={()=>{this.setState({userModal: false})}}
             footer={null}
             width={600}
             >
         <Form 
 //    onFinish={this.onFinish} 
    layout="vertical">
    <Row gutter={24}>
    <Col span={3}></Col>
    <Col span={17}>
    <Form.Item
     name="name"
     label="Name"
     rules={[
       {
         required: true,
         message: 'Please Enter Product Parent Code!',
       },
     ]}
   >
     <Input style={{width:'100%'}}/>
   </Form.Item>
     </Col>
     </Row>
     <Row>
     <Col span={3}></Col>
     <Col span={17}>
    <Form.Item
     name="role"
     label="Role"
     rules={[
       {
         required: true,
         message: 'Please Enter Product Parent Code!',
       },
     ]}
   >
    <Select mode="multiple" placeholder="Please Select Role" >
     {role.map((c) => {
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
     </Form>
     </Modal>
      </App>
          )
    }else{
      return(
        <Appm>
            <h4 style={{color:'#48486c', textAlign:'center', fontWeight:'bold',fontSize:"19px",marginTop:"1%"}}>Purchase Order</h4> 
            <Row style={{marginTop:"10px",height: 580,width:"100%", overflow:'auto'}}>
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

export default CreatePO