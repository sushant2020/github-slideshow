import React, {Component} from "react";
import {Table, Divider, Button, Modal ,Space , Tag, Popconfirm, Form, Row, Col, Input, Select} from "antd";
import App from "../App";
// import { withApollo } from "react-apollo";

import {
    EyeOutlined,
    EditOutlined,
    DeleteOutlined
  
  } from '@ant-design/icons';
  
  const { Option } = Select;
  const { Search } = Input;
  
  const data = [
    {
      key: '1',
      name: 'ALLPHA',
      age: 32,
      phone: '9988776655',
      address: 'New York No. 1 Lake Park',
      code: 'MO13',
      email: 'john.brown@gmail.com',
      status: 'Active',
      tags: ['developer'],
    },
    {
      key: '2',
      name: 'RECKIT',
      age: 42,
      phone: '9988776644',
      address: 'London No. 1 Lake Park',
      code: '400-1681',
      email: 'jim.green@gmail.com',
      status: 'Active',
      tags: ['Administrator'],
    },
    {
      key: '3',
      name: 'SOLVAY',
      age: 32,
      phone: '9988776633',
      address: 'Sydney No. 1 Lake Park',
      code: '2TRI',
      email: 'joe.black@gmail.com',
      status: 'Active',
      tags: ['Manager'],
    },
    {
      key: '4',
      name: 'NEOLAB',
      age: 32,
      phone: '9988776622',
      address: 'London, Park Lane no. 2',
      code: '1FAM42',
      email: 'joe.black@gmail.com',
      status: 'Active',
      tags: ['Manager'],
    },
    {
      key: '5',
      name: 'BELLS',
      age: 32,
      phone: '9988776611',
      address: 'London, Park Lane no. 3',
      code: '3ALM50',
      email: 'joe.black@gmail.com',
      status: 'Active',
      tags: ['Manager'],
    },
    {
      key: '6',
      name: 'DEXCEL',
      age: 32,
      phone: '9988776612',
      address: 'London, Park Lane no. 4',
      code: '1ENA20',
      email: 'joe.black@gmail.com',
      status: 'Active',
      tags: ['Manager'],
    },
    {
      key: '7',
      name: 'TILLOMED',
      age: 32,
      phone: '9988776613',
      address: 'London, Park Lane no. 5',
      code: '1ENA52',
      email: 'joe.black@gmail.com',
      status: 'Active',
      tags: ['Manager'],
    },
  ];
  
  const role = [
    { value: "Administrator", label: "Administrator" },
    { value: "Buyer", label: "Buyer" },
    { value: "Manager", label: "Manager" },
    { value: "Staff", label: "Staff" },
  ];

class ChildProducts extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           collapsed: false,
           tableData: [],
        };
      }

    componentDidMount(){
        // console.log("In CDM");
    }

    onSearch=(n)=>{
        // console.log("In Search",n)
   
         // let index = data.findIndex((item) => (item.key == n) ||  (item.qty == n) ||  (item.price == n) ||  (item.note == n))
         // const found = data.find(element => element.name == n);
         let arr = [];
         data.map((i, j) => {
           if (i.name.toLowerCase().includes(n.toLowerCase()) || i.key.includes(n) 
           || i.phone.toLowerCase().includes(n.toLowerCase())
            || i.code.toLowerCase().includes(n.toLowerCase())
            || i.address.toLowerCase().includes(n.toLowerCase())
            ) {
             arr.push(i);
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
         if(n == ''){
          // console.log("Search No Result:: ")
           
           this.setState({
             tableData: data
           })
         }
       }

    render(){
        // console.log("In Render");

        const columns = [
            {
              title: 'No',
              dataIndex: 'key',
            },
            {
              title: 'Name',
              // dataIndex: 'name',
              key: 'name',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold'}}>{data.name}</h3>
                )
              }
            },
            {
              title: 'Code',
              // dataIndex: 'email',
              key: 'code',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold'}}>{data.code}</h3>
                )
              }
            },
            {
              title: 'Phone',
              // dataIndex: 'email',
              key: 'phone',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold'}}>{data.phone}</h3>
                )
              }
            },
            {
              title: 'Address',
              // dataIndex: 'email',
              key: 'address',
              render:(data)=>{
                // console.log("Data..",data)
                return(
                  <h3 style={{fontWeight:'bold'}}>{data.address}</h3>
                )
              }
            },
            
            {
              title: 'Action',
              key: 'action',
              render: (text, record) => (
                <>
                 <Space size="middle">
                  {/* <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'view'})}} icon={<EyeOutlined style={{fontSize:25}}/>} style={{background:"#353b8d", borderColor:"#353b8d"}}/>  */}
                  <Button type="primary" size="large" onClick={()=>{this.setState({viewModal: true, action: 'edit'})}} icon={<EditOutlined style={{fontSize:25}}/>} style={{marginLeft:"10px",background:"#353b8d",borderColor:"#353b8d"}}
                  //onClick={() => this.handleMatch(data.prodId)}
                  /> 
                  <Popconfirm title="Sure to delete?" 
                  //onConfirm={() => this.handleDelete(data.prodId)}
                  >
                          <Button icon={<DeleteOutlined style={{fontSize:25}}/>} size="large" type= "danger"  style={{marginLeft:"10px",cursor: "pointer", background:"#8f0021",borderColor:"#8f0021" }}/>
                  </Popconfirm> 
                  {/* <DeleteOutlined /> */}
                 </Space>
                </>
              ),
            },
          ];

     return(
         <App>
             <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
               {/* Products Overview  */}
             </Divider>

             {/* <Search placeholder="Search" 
         onSearch={(val)=>this.onSearch(val)} 
        //  onChange={(val)=>this.onSearch(val)} 
         allowClear style={{width:'30%'}} /> */}

             {/* <Table columns={columns} 
             expandable={{expandedRowRender: record => <Row><Col><p style={{ margin: 0 }}>Name</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.name}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Code</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.code}</p></Col><Col offset={5}><p style={{ margin: 0, }}>Phone</p><p style={{ margin: 0,fontWeight: 'bold' }}>{record.phone}</p></Col></Row>,rowExpandable: record => record.name !== 'Not Expandable',}} 
             dataSource={this.state.tableData.length > 0 ? this.state.tableData : data} style={{margin:'2%'}} className='product' /> */}

         </App>
     )   
    }

}

export default ChildProducts