import React, {Component} from "react";
import {Form,Button, Row, Col, Input, Select, Layout, Modal, Card, message} from "antd";
import App from "../App";
import axios from 'axios';
// import { withApollo } from "react-apollo";
import CreateUser from "./createUser"
import { EyeInvisibleOutlined, EyeTwoTone } from '@ant-design/icons';

const { Option } = Select;

let currentpassword = "Test@1234"

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

class Profile extends Component {
    constructor(props) {
        super(props);
        this.state = {
           form: false,
           userModal: false,
           selected: 'PI',
           fname:'',
           lname:'',
           currPass: '',
           newPass:'',
           verPass:'',
           userDetails: {},
          //  email:'',
          //  role:'',
          //  reg:'',

        };
      }

    componentDidMount(){
        // console.log("In CDM");
        let userData = JSON.parse(localStorage.getItem('portalUserData'));

        // console.log("USER>>>",userData);

        if(userData == null ){
          // this.props.history.push('/')
        }else{
          this.setState({
            userDetails: userData
          })
        }

       let user = 'https://api.sigmaproductmaster.webdezign.uk/api/myprofile'

       axios.get(user).then((response) => {
        // console.log("Response...::",response.data)
        if(response){
        // this.setState({
        //   userData: response.data.users,
        //   userloading: false
        // })
      }else{
        this.setState({
          // prodData: response.data,
          prodloading: false
        })
      }
      })
    }

    changePassword=()=>{

     let cp = this.state.currPass , np = this.state.newPass, vp = this.state.verPass

     if(cp != ''){
        let result = currentpassword.match(cp)
        // console.log("CP !!!",result)
        if(result != null && result.index == 0){
          this.setState({currPass: currentpassword})
          // message.success("Password Matched")
          if(np != '' && vp != ''){
      let result = np.match(vp)
      // console.log("result !!!",result)
      if(result != null && result.index == 0){
        this.setState({currPass: currentpassword})
        message.success("Password Matched & Changed Successfully")
      }else{
        this.setState({currPass: currentpassword})
        message.error("Password Incorrect didn't Matched !!")
      }
     }
        }else{
          this.setState({currPass: currentpassword})
          message.error("Current Password Incorrect!!")
        }
     }
     
    }

    submit=()=>{

      this.setState({
        fname: ''
      })
      message.success('Updated Successfully')
    }

    render(){
        console.log("In Render",this.state.userDetails.firstname);
     return(
         <App>
           {/* <Divider orientation="left" style={{ color: "#333", fontWeight: "bold" }}>
               Profile 
             </Divider> */}
           <div style={{margin:10}}>
             <h4 style={{color:'#8F0021', fontWeight:'bold', fontSize:34,textAlign:'center'}}>{this.state.userDetails.firstname} {this.state.userDetails.lastname}</h4>
             <h4 style={{color:'#051713', fontWeight:'bold', fontSize:22,marginLeft:100}}>Email : 
             &nbsp;<span style={{color:'#213A87', fontWeight:'bold', fontSize:22}}>{this.state.userDetails.email}</span>
             </h4>
            
             <Row style={{marginTop:30}}>
             <Col span={7}></Col>
             <Col span={4}>
            <Button block size="large" onClick={()=>{this.setState({selected: 'PI'})}}
            style={{color:'#213A87', fontWeight:'bold', background: this.state.selected == 'PI' ? '#CDD9E0' : '' ,borderColor: this.state.selected == 'PI' ? '#CDD9E0' : ''}}>
              Personal Information</Button>
            </Col>
             <Col span={4} offset={2}>
             <Button size="large" block onClick={()=>{this.setState({selected: 'CP'})}}
             style={{color:'#213A87', fontWeight:'bold', background: this.state.selected == 'CP' ? '#CDD9E0' : '', borderColor: this.state.selected == 'CP' ? '#CDD9E0' : ''}}> 
             Change Password </Button>
             </Col>
             <Col span={2}></Col>
             </Row>


             <Card style={{margin:5,marginTop:40}}>
               {this.state.selected == "PI" &&
                <>
             <h4 style={{color:'#8F0021', fontWeight:'bold', fontSize:24,textAlign:'center'}}>Personal Information
             &nbsp;<span style={{color:'#051713',fontWeight:'normal', fontSize:15}}>update your personel information</span>
             </h4>

             <Form layout="vertical" 
            //  onFinish={message.success('Updated Successfully')}
             >
             <Row gutter={24} style={{marginTop: 30}}>
            <Col span={12}>
            <Form.Item 
            name="fName" 
            label="First Name : " 
            initialValue={this.state.userDetails.firstname}
            rules={[
          {
            required: true,
            message: 'Please Enter First Name',
          },
        ]}
           >
        <Input  style={{}}/>
      </Form.Item>
            </Col>
            <Col span={12}>
            <Form.Item 
            name="lName" 
            label="Last Name : " 
            initialValue={this.state.userDetails.lastname}
            rules={[
          {
            required: false,
            message: 'Please Enter Last Name',
          },
        ]}
      >
        <Input  />
      </Form.Item>
            </Col>
            <Row gutter={24}></Row>
            <Col span={12}>
            <Form.Item  name="role" label="Role : " 
        //     rules={[
        //   {
        //     required: true,
        //     message: 'Please Enter First Name',
        //   },
        // ]}
      >
        <Input disabled="true" defaultValue="Administrator" style={{}}/>
      </Form.Item>
            </Col>
            <Col span={12} style={{fontSize:18,}}>
            <Form.Item  name="reg" label="Reg On : " 
            // initialValue={this.state.userDetails.firstname}
            rules={[
          {
            required: false,
            message: 'Please Enter Date',
          },
        ]}
      >
        <Input disabled="true" defaultValue="27/02/2021" style={{}}/>
      </Form.Item>
            </Col>
            <Col span={24}>
            <h4 style={{color:'#8F0021', fontSize:28,}}>Contact Info : </h4>
            </Col>
            <Col span={12}>
            <Form.Item name="email" label="Email : " initialValue={this.state.userDetails}
            rules={[
          {
            required: false,
            message: 'Please Enter Email',
          },
        ]}
      >
        <Input disabled="true" defaultValue="atul@webdezign.co.uk" style={{}}/>
      </Form.Item>
            </Col>
            </Row>
            <Row>
      <Col span={11}></Col>
      <Col span={10} style={{marginLeft:'3%'}}>
      <Button size="large" type="primary" style={{marginLeft:'1%',marginTop: 30, background:'#213A87'}} htmlType="submit" onClick={this.submit}>
        Save
        </Button>
      {/* <Button style={{marginLeft:'5%',marginTop:'3%'}} onClick={()=>this.setState({mainPage:true})}>Back</Button> */}
      </Col>
      {/* <Col xs={{ span: 24 }} sm={{ span: 24 }} md={{ span: 6 }} lg={{ span: 6 }} ></Col> */}
      </Row>
      </Form>
      </>
           }
           {this.state.selected == "CP" &&
                <>
             <h4 style={{color:'#8F0021', fontWeight:'bold', fontSize:24,textAlign:'center'}}>Change Password
             &nbsp;<span style={{color:'#051713',fontWeight:'normal', fontSize:15}}>update your password</span>
             </h4>
             <Form layout="vertical">
             <Row gutter={24} style={{marginTop: 30}}>
            <Col span={8}>
            
            </Col>
            <Col span={8}>
            <Form.Item name="cPassword" label="Current Password :"
            rules={[
              {
                required: true,
                message: 'Current Password Required !',
              },
            ]}>
        <Input.Password style={{}} iconRender={visible => (visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />)}
          onChange={(e)=>this.setState({currPass:e.target.value})}
        />
      </Form.Item>
            </Col>
            <Col span={8}>
            
            </Col>
            </Row>

            <Row gutter={24}>
            <Col span={8}>
            
            </Col>
            <Col span={8}>
            <Form.Item name="nPassword" label="New Password : " rules={[
              {
                required: true,
                message: 'New Password Required !',
              },
            ]}>
        <Input.Password onChange={(e)=>this.setState({newPass:e.target.value})} style={{}} iconRender={visible => (visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />)}/>
      </Form.Item>
            </Col>
            <Col span={8}>
            
            </Col>
            </Row>
            
            <Row gutter={24}>
            <Col span={8}>
            
            </Col>
            <Col span={8}>
            <Form.Item name="vPassword" label="Verify Password : " rules={[
              {
                required: true,
                message: 'Veriry Password Required !',
              },
            ]}>
        <Input.Password onChange={(e)=>this.setState({verPass:e.target.value})} style={{}} iconRender={visible => (visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />)}/>
      </Form.Item>
            </Col>
            <Col span={8}>
            
            </Col>
            </Row>
            <div style={{textAlign:"center"}}>
               <Button type="primary" htmlType="submit"
                 onClick={()=>{this.changePassword()}}
                size="large" style={{marginTop: 30, background:'#213A87', borderColor:"#213A87"}}>
                  Change Password
            </Button>
            </div>
            </Form>
            
            {/* <Row>
      <Col span={11}></Col>
      <Col span={10} style={{marginLeft:'1%'}}>
      <Button size="large" type="primary" style={{marginTop: 30, background:'#213A87', borderColor:"#213A87", boxShadow:"0 0 5px"}} htmlType="submit">Change Password</Button>
      
      </Col>
      
      </Row> */}
      </>
           }
             </Card>
           </div>
            
         </App>
     )   
    }

}

export default Profile