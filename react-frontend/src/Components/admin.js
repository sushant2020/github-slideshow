import React, {Component} from "react";
import {Table, Divider, Button, Row, Col, Alert, message, Popconfirm} from "antd";
import App from "../App";
// import { withApollo } from "react-apollo";

class Admin extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
        collapsed: false
        };
      }

    componentDidMount(){
        console.log("In CDM");
    }

    render(){
        // console.log("In Render");
     return(
         <App>
             <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
               Products Overview 
             </Divider>
         </App>
     )   
    }
}

export default Admin