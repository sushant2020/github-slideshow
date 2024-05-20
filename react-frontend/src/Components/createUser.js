import React from 'react'
import { Form,Button, Row, Col, Input, Select,Layout,message } from "antd";
import axios from 'axios';

const { Option } = Select;
const { Header, Content, Footer } = Layout;

const role = [
  { value: "Administrator", label: "Administrator" },
  { value: "Buyer", label: "Buyer" },
  { value: "Manager", label: "Manager" },
  { value: "Staff", label: "Staff" },
];

class CreateUser extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
          mainPage: false,
        };
      }
      formRef = React.createRef();

    onFinish = (values) => {

        // console.log('Received values of form: ', values);
        // this.props.closeModal();
      // return
        // let roles = '' 
      // roles = values.role.map((p)=>{ return p})
      // console.log('Received values of form role:  ', roles);
      //  return
        const user = {
          firstname : values.fName,
          lastname : values.lName,
          email : values.email,
          role : values.role
        }
        // console.log('Received values of form: ',user);
        // return
        let createUser="https://api.sigmaproductmaster.webdezign.uk/api/createuser"

      axios({
        method: "post",
        url: createUser,
        data: user,
        // headers: { "Content-Type": "multipart/form-data" },
      })
      .then((response) => {
        console.log("Response...::",response)

        if(response.data.status == "success"){
          console.log("Response...::",response.data)
          // localStorage.setItem("userDetails",response.data);
          this.setState({
            userDataRes: response.data
          })
          // message.success('User Created Successfully !');
          // return
        
        }else{
          this.setState({
          userDataRes: response.data.error,
          
        })
        // message.error(' User Creation Unsuccesfull !! ');
        // setTimeout(function(){ this.setState({
        //   loginStatus: false,  
        // }) }, 3000);
        }
      })

        message.success('user created Successfully');
        this.props.getUser();
        this.formRef.current.resetFields();
        this.props.closeModal();
    };


  // const { getFieldDecorator } = this.props.form;
  render(){
  return (
    <div>
       
       <Form 
       onFinish={this.onFinish} 
       layout="vertical"
       ref={this.formRef}
       >
       <Row gutter={24}>
       <Col span={3}></Col>
       <Col span={17}>
       <Form.Item
        name="fName"
        label="First Name"
        rules={[
          {
            required: true,
            message: 'Please Enter First Name',
          },
        ]}
      >
        <Input style={{width:'100%'}}/>
      </Form.Item>
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17} >
        <Form.Item
        name="lName"
        label="Last Name"
        rules={[
          {
            required: true,
            message: 'Please Enter Last Name',
          },
        ]}
      >
        <Input style={{width:'100%'}}/>
      </Form.Item> 
        </Col>
        </Row>
        <Row>
        <Col span={3}></Col>
        <Col span={17} >
        <Form.Item
        name="email"
        label="Email"
        rules={[
          {
            required: true,
            message: 'Please Enter Email-Id',
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
            message: 'Please Select Role',
          },
        ]}
      >
       <Select 
      //  mode="multiple" 
       placeholder="Please Select Role" >
        {role.map((c) => {
          // console.log("cccc",c)
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
      

      <Row>
      <Col span={9}></Col>
      <Col span={13} style={{marginLeft:'3%'}}>
      <Button type="primary" style={{backgroundColor:'#353b8d', borderColor:'#353b8d', marginLeft:'1%',marginTop:'3%'}} htmlType="submit">Submit</Button>
      {/* <Button style={{marginLeft:'5%',marginTop:'3%'}} onClick={()=>this.setState({mainPage:true})}>Back</Button> */}
      </Col>
      </Row>

      </Form>
      
    </div>
  );
    }
}

export default CreateUser;
