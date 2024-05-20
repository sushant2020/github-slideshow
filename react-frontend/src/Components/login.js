import React, {Component} from "react";
import {Table, Divider, Button, Layout, Card, Modal, Form, Row, Col, Input,message,Spin} from "antd";
import { Link, BrowserRouter as Router, withRouter, Switch} from 'react-router-dom';
import MediaQuery from 'react-responsive';
import axios from 'axios';
import validator from 'validator';

// import App from "../App";
// import { withApollo } from "react-apollo";

import {
  EyeOutlined,
  EditOutlined,
  DeleteOutlined,
  MinusCircleOutlined, 
  PlusOutlined,
  EditFilled,
  HighlightFilled,
  DeleteFilled,
  LoadingOutlined

} from '@ant-design/icons';

const antIcon = <LoadingOutlined style={{ fontSize: 24 }} spin />;

const { Header, Content, Footer, Sider } = Layout;

class Login extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
        collapsed: false,
        landingModal: false,
        forgotPassward: false,
        username:"",
        password:"",
        loginStatus: false,
        loginMsg: "",
        aftSubmit: false,
        emailErr: false,
        // username1:"",
        // password1:"",
        };
      }

    componentDidMount(){
        // this.setState({
        //   loginStatus: "",
        //   loginMsg: ""
        // })
        // console.log("In CDM Login");
        // setTimeout(() => {  this.setState({landingModal: true}) }, 2000);
    }

    submit=()=>{
      // console.log("Submit",this.state.username,this.state.password);

      // let usename = "atul@webdezign.co.uk"
      // let password = "atul@123"
      // return
      if(this.state.username != "" && this.state.password != ""){
        let result = validator.isEmail(this.state.username)
        // console.log("Validate REsult::",result)
        if(result == true){
          // console.log("Reult in if");
          this.setState({
            // username: e,
            emailErr: false
          })
       
        const user = {
          email : this.state.username,
          password : this.state.password
        }
        this.setState({
          aftSubmit: true
        })
        // var Data = new FormData();
        // Data.append('username', `${this.state.username}`);
        // Data.append('password', `${this.state.password}`);
        // console.log("Submit in If::",user);
        // return
      // let loginApi="https://sigmaproductmaster.mywebdezign.uk/api/login"
        let loginApi="https://api.sigmaproductmaster.webdezign.uk/api/login"

      axios({
        method: "post",
        url: loginApi,
        data: user,
        // headers: { "Content-Type": "multipart/form-data" },
      })
      .then((response) => {
        console.log("Response...::",response.data)  

        
        if(response.data.status == "success"){
          // console.log("Response Succ...::",response.data.data)
          
          //  let firstname = response.data.data.firstname,
          //      lastname = response.data.data.lastname,
          //      email = response.data.data.email,
          //      id = response.data.data.id,
          //      created_at = response.data.data.created_at;

          // console.log("Response Succ User.::",firstname,lastname,email,id,created_at)
         
          
          localStorage.setItem("portalUserData",JSON.stringify(response.data.data));
          // localStorage.setItem("portalUserName",firstname);
          // localStorage.setItem("portalUserLast",lastname);
          // localStorage.setItem("portalUserEmail",email);
          // localStorage.setItem("portalUserId",id);
          // localStorage.setItem("portalUserCreated",created_at);

          
          this.setState({
            loginStatus: false,
            loginMsg: response.data.error,
            // username:"",
            // password:"",
            aftSubmit: false
          })
          message.success('Login In Successfully !');
          // return
          this.props.history.push('/products')
        }else{
          this.setState({
          loginStatus: true,
          loginMsg: response.data.error,
          // username:"",
          // password:"",
          aftSubmit: false
        })
        message.error(' Login Unsuccesfull !! ');
        // setTimeout(function(){ this.setState({
        //   loginStatus: false,  
        // }) }, 3000);
        }
      })
       }else{
          this.setState({
            // username: "",
            emailErr: true
          })
        }
      }
    // }
    }

    emailValid=(e)=>{

      // console.log("email..",e);
      let result = validator.isEmail(e)
      // console.log("Validate REsult::",result)
      if(result == true){
        // console.log("Reult in if");
        this.setState({
          username: e,
          emailErr: false
        })
      }else{
        this.setState({
          username: "",
          emailErr: true
        })
      }
    }

    render(){
        // console.log("In Render",this.state.loginStatus);
       const { loginStatus } = this.state;
    return(
      <MediaQuery minDeviceWidth={900}>
      {(matches) => {
          if (matches) {
          return (
      <Layout>
      <Header className="header" style={{width: '100%', background: '#fdfdfd', height: '70px', padding: "0px"}}> 
        <img alt="example" src="./sigma_logo.png" style={{maxHeight:'70px',marginLeft:'0px'}}/>
      </Header>
      <Content>
       <div style={{minHeight: 900}}>
         <div style={{alignContent:"center"}}>
         <Spin indicator={antIcon} spinning={this.state.aftSubmit}>
             {/* <h4 style={{textAlign:"center",fontSize:"25px",fontWeight:"bold",marginTop:"5%",color:'#213A87'}}>LogIn</h4> */}
             <h4 style={{fontSize:"23px",fontWeight:"bold",color:'#213A87',textAlign:"center",marginTop:"6%"}}>Welcome to Product Portal</h4>

             <Card style={{width: '30%',boxShadow:"1px 1px 5px",marginLeft:"35%",borderRadius:5,padding:"10px"}}>
             {/* <h4 style={{fontSize:"22px",fontWeight:"bold",marginLeft:"6%",color:'#213A87'}}>Welcome to Product Portal</h4> */}
             
             {this.state.forgotPassward != true ?
             <>
             <h4 style={{marginLeft:"7%", fontSize:"21px",fontWeight:"bold",color:'#213A87'}}>Login</h4>
             <Form layout="vertical" style={{marginLeft:"6%"}}
            //  onFinish={message.success('Updated Successfully')}
            onFinish={this.submit}
             >
             <Row gutter={24} style={{marginTop: 30}}>
            <Col span={21}>
            <Form.Item className='label'  name="fName" label="Username : "
            rules={[
          {
            required: true,
            message: 'Please Enter Username',
          },
        ]}
           >
        <Input size="large" 
        // onChange={(e)=>{this.emailValid(e.target.value)}} 
        onChange={(e)=>{this.setState({username: e.target.value})}}
        style={{boxShadow:"0 0 2px",borderRadius:7}}
        />
      </Form.Item>
         {this.state.emailErr == true &&
            <h4 style={{color:"#ff4d4f",fontWeight:"normal",fontSize: "14px"}}> Please enter a valid email address </h4>
         }
            </Col>
            </Row>

            <Row>
            <Col span={21}>
            <Form.Item className='label'  name="lName" label="Password : "
            rules={[
          {
            required: true,
            message: 'Please Enter Password',
          },
        ]}
      >
        <Input size="large" type="password" onChange={(e)=>{this.setState({password: e.target.value})}} style={{boxShadow:"0 0 2px",borderRadius:7}}/>

        {/* <Button size="large" type="link" 
        style={{marginLeft:'-4%'}}  
        onClick={()=>this.setState({forgotPassward: !this.state.forgotPassward})}
        >
         forgot password ?
        </Button> */}

      </Form.Item>
      
            </Col>
            </Row>
            {loginStatus == true &&
            <h4 style={{color:"red"}}> {this.state.loginMsg} </h4>
            }
        <Button size="large" type="primary" 
        style={{marginLeft:'1%',marginTop: 30, borderRadius:5, boxShadow:"0 0 5px"}} 
        htmlType="submit"
        //onClick={()=>this.submit()}
        >
        SignIn
        </Button>
            </Form>

            </>
            :
            <>
             <h4 style={{marginLeft:"7%", fontSize:"21px",fontWeight:"bold",color:'#213A87'}}>Forgot Passward ?</h4>
             <Form layout="vertical" style={{marginLeft:"6%"}}
            //  onFinish={message.success('Updated Successfully')}
             >
             <Row gutter={24} style={{marginTop: 30}}>
            <Col span={21}>
            <Form.Item className='label'  name="email" label="Enter your email to reset your password : "
            rules={[
          {
            required: true,
            message: 'Please Enter Email',
          },
        ]}
           >
        <Input size="large" style={{boxShadow:"0 0 2px",borderRadius:7}}/>
      </Form.Item>

            </Col>
            </Row>

            {/* <Row>
            <Col span={21}>
            <Form.Item className='label'  name="lName" label="Password : "
            rules={[
          {
            required: true,
            message: 'Please Enter Password',
          },
        ]}
      >
        <Input size="large" style={{boxShadow:"0 0 2px",borderRadius:7}}/>

        <Button size="large" type="link" 
        style={{marginLeft:'-4%'}}  
        // onClick={this.submit}
        >
         forgot password ?
        </Button>

      </Form.Item>
      
            </Col>
            </Row> */}
            <Row>
              <Col>
        <Button size="large" type="primary" 
        style={{marginLeft:'1%',marginTop: 30, borderRadius:5, boxShadow:"0 0 5px"}} 
        // onClick={()=>this.submit()}
        >
        Submit
        </Button>
         </Col>
         <Col offset={1}>
         <Button size="large" 
        style={{marginLeft:'1%',marginTop: 30, borderRadius:5, boxShadow:"0 0 5px"}} onClick={()=>this.setState({forgotPassward: false})}>
        Cancel
        </Button>
         </Col>
         </Row>
            </Form>

            </>
           }
            </Card>
            </Spin>
          </div>
        </div> 
        </Content>
        </Layout>  
        )}
        else{
          return(
            <Layout>
            <Header className="header" style={{width: '100%', background: '#fdfdfd', height: '60px', padding: "0px"}}> 
              <img alt="example" src="./sigma_logo.png" style={{maxHeight:'55px',marginLeft:'0px'}}/>
            </Header>
            <Content>
             <div style={{minHeight: 700}}>
               <div style={{alignContent:"center"}}>
               <Spin indicator={antIcon} spinning={this.state.aftSubmit}>
                   {/* <h4 style={{textAlign:"center",fontSize:"25px",fontWeight:"bold",marginTop:"5%",color:'#213A87'}}>LogIn</h4> */}
                   <h4 style={{fontSize:"23px",fontWeight:"bold",color:'#213A87',textAlign:"center",marginTop:"6%"}}>Welcome to Product Portal</h4>
      
                   <Card style={{margin:"5%",width: '90%',boxShadow:"1px 1px 5px",borderRadius:5,padding:"10px"}}>
                   {/* <h4 style={{fontSize:"22px",fontWeight:"bold",marginLeft:"6%",color:'#213A87'}}>Welcome to Product Portal</h4> */}
                   
                   {this.state.forgotPassward != true ?
                   <>
                   <h4 style={{marginLeft:"7%", fontSize:"21px",fontWeight:"bold",color:'#213A87'}}>Login</h4>
                   <Form layout="vertical" style={{marginLeft:"6%"}}
                  //  onFinish={message.success('Updated Successfully')}
                  onFinish={this.submit}
                   >
                   <Row gutter={24} style={{marginTop: 30}}>
                  <Col span={21}>
                  <Form.Item className='label'  name="fName" label="UserName : "
                  rules={[
                {
                  required: true,
                  message: 'Please Enter UserName',
                },
              ]}
                 >
              <Input size="large" onChange={(e)=>{this.setState({username: e.target.value})}} style={{boxShadow:"0 0 2px",borderRadius:7}}/>
            </Form.Item>
      
                  </Col>
                  </Row>
      
                  <Row>
                  <Col span={21}>
                  <Form.Item className='label'  name="lName" label="Password : "
                  rules={[
                {
                  required: true,
                  message: 'Please Enter Password',
                },
              ]}
            >
              <Input size="large" onChange={(e)=>{this.setState({password: e.target.value})}} style={{boxShadow:"0 0 2px",borderRadius:7}}/>
      
              {/* <Button size="large" type="link" 
              style={{marginLeft:'-4%'}}  
              onClick={()=>this.setState({forgotPassward: !this.state.forgotPassward})}
              >
               forgot password ?
              </Button> */}
      
            </Form.Item>
            
                  </Col>
                  </Row>
              <Button size="large" type="primary" 
              style={{marginLeft:'1%',marginTop: 30, borderRadius:5, boxShadow:"0 0 5px"}} 
              // onClick={()=>this.submit()}
              htmlType="submit"
              >
              SignIn
              </Button>
                  </Form>
      
                  </>
                  :
                  <>
                   <h4 style={{marginLeft:"7%", fontSize:"21px",fontWeight:"bold",color:'#213A87'}}>Forgot Passward ?</h4>
                   <Form layout="vertical" style={{marginLeft:"6%"}}
                  //  onFinish={message.success('Updated Successfully')}
                   >
                   <Row gutter={24} style={{marginTop: 30}}>
                  <Col span={21}>
                  <Form.Item className='label'  name="email" label="Enter your email to reset your password : "
                  rules={[
                {
                  required: true,
                  message: 'Please Enter Email',
                },
              ]}
                 >
              <Input size="large" style={{boxShadow:"0 0 2px",borderRadius:7}}/>
            </Form.Item>
      
                  </Col>
                  </Row>
      
                  <Row>
                    <Col>
              <Button size="large" type="primary" 
              style={{marginLeft:'1%',marginTop: 30, borderRadius:5, boxShadow:"0 0 5px"}} onClick={()=>this.submit()}>
              Submit
              </Button>
               </Col>
               <Col offset={1}>
               <Button size="large" 
              style={{marginLeft:'1%',marginTop: 30, borderRadius:5, boxShadow:"0 0 5px"}} onClick={()=>this.setState({forgotPassward: false})}>
              Cancel
              </Button>
               </Col>
               </Row>
                  </Form>
      
                  </>
                 }
                  </Card>
                  </Spin>
                </div>
              </div> 
              </Content>
              </Layout>
            )
        }
      } 
    }
    </MediaQuery>
     )   
    }

}

export default withRouter(Login)