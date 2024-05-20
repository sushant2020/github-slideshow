import React, {Component} from "react";
import {Form, Button, Row, Col, Input, Select, Modal, Divider, Table, Tag, Popconfirm ,Space,message } from "antd";
import App from "../App";


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

 
class PowerBI extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
          
        };
      }

    componentDidMount(){
        // console.log("In CDM");
    }

    
    render(){
     
      
     return(
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
        
        <iframe width="1424" height="612" src="https://app.powerbi.com/view?r=eyJrIjoiZWQ3OWQ3N2MtYzI0MS00Njc5LWE2ODktYjE1M2I1ZWFlZTM5IiwidCI6IjFjNWMzYzk2LTcwMTgtNDk2YS1hNDIwLWJjMTNiMDk1OWVmMyJ9" frameborder="0" allowFullScreen="true"></iframe> 

        <iframe width="1424" height="612" src="https://app.powerbi.com/view?r=eyJrIjoiZWQ3OWQ3N2MtYzI0MS00Njc5LWE2ODktYjE1M2I1ZWFlZTM5IiwidCI6IjFjNWMzYzk2LTcwMTgtNDk2YS1hNDIwLWJjMTNiMDk1OWVmMyJ9" frameborder="0" allowFullScreen="true"></iframe>

        <iframe width="1424" height="612" src="https://app.powerbi.com/view?r=eyJrIjoiZWQ3OWQ3N2MtYzI0MS00Njc5LWE2ODktYjE1M2I1ZWFlZTM5IiwidCI6IjFjNWMzYzk2LTcwMTgtNDk2YS1hNDIwLWJjMTNiMDk1OWVmMyJ9" frameborder="0" allowFullScreen="true"></iframe>

        <iframe width="1424" height="612" src="https://app.powerbi.com/view?r=eyJrIjoiZWQ3OWQ3N2MtYzI0MS00Njc5LWE2ODktYjE1M2I1ZWFlZTM5IiwidCI6IjFjNWMzYzk2LTcwMTgtNDk2YS1hNDIwLWJjMTNiMDk1OWVmMyJ9" frameborder="0" allowFullScreen="true"></iframe> 
        
      </App>
     )   
    }

}

export default PowerBI