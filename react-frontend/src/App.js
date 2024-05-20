import React from 'react'
import './App.css';
import { Button, Table, Layout, Menu, Popover, Dropdown, Card, notification} from "antd";
import { Link, BrowserRouter as Router, withRouter, Switch} from 'react-router-dom';
import MediaQuery from 'react-responsive';
import Appm from './mApp'
import axios from 'axios';
// import Products from "./Components/products"
// import Home from "./Components/home"
// import CreatePO from "./Components/createPO"
// import Admin from "./Components/admin"
// import Supplier from "./Components/supplier"

import {
  AppstoreOutlined,
  PlusOutlined,
  MenuUnfoldOutlined,
  MenuFoldOutlined,
  LogoutOutlined,
  CodeSandboxCircleFilled,
  PieChartOutlined,
  UserOutlined,
  HomeOutlined,
  ProfileOutlined,
  ClusterOutlined,
  AppstoreAddOutlined,
  CheckCircleOutlined,
  ContainerOutlined,
  MailOutlined,
  ProjectOutlined,
  ShopOutlined,
  TeamOutlined,
  CaretDownOutlined,
  BellTwoTone,
  CloseOutlined,
  BarChartOutlined,
  InboxOutlined
} from '@ant-design/icons';

const { Header, Content, Footer, Sider } = Layout;
const { SubMenu } = Menu;

const productClassification = [
  { value: "GENERIC", label: "GENERIC" },
  { value: "Vitamins", label: "Vitamins" },
  { value: "Region", label: "Region" },
  { value: "Price", label: "Price" },
  { value: "Quantity", label: "Quantity" },
  { value: "Dose", label: "Dose" },
];

const productFeatures = [
  { value: "Rating", label: "Rating" },
  { value: "Strength", label: "Strength" },
  { value: "GENERIC", label: "GENERIC" },
];

let temp = [], temp1 = []
class App extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
    form: false,
    visiblePop: false,
    collapsed: false,
    clasiModal: false,
    featuresModal: false,
    purchaseModal: false,
    selected: false,
    prodFeature: [],
    prodClasi: [],
    activemenuKey: '',
    defaultOpen: [],
    landingModal: false,
    notif: false,
    userDetails: {},
    };
  }

  componentDidMount(){
    setTimeout(() => {  this.setState({landingModal: true}) }, 5000);

    let userData = JSON.parse(localStorage.getItem('portalUserData'));

    if(userData == null ){
      // this.props.history.push('/')
    }else{
      this.setState({
        userDetails: userData
      })
    }
    // let logout = "https://api.sigmaproductmaster.webdezign.uk/api/logout"
    // axios.get(logout).then((response) => {
    //   console.log("Response...::",response.data)
    //   if(response){
    //   this.setState({
    //     // prodData: response.data,
    //     // prodloading: false
    //   })
    // }else{
    //   this.setState({
    //     // prodData: response.data,
    //     // prodloading: false
    //   })
    // }
    // })

  }
  
  static showMenuSelected(url) {
    const pathArr = url.split('/').filter(pathVal => pathVal != '');
    const pathName = pathArr[0];
    let activeKey = '0';
    let defaultOpen = [];

    switch (pathName) {
      case undefined:
        activeKey = '1';
        break;
        case 'home':
        activeKey = '1';
        break;
        case 'products':
        activeKey = '2';
        break;
        case 'pricingDetails':
        activeKey = 'pricingDetails';
        break;
      case 'profile':
        activeKey = '31';
        break;
        case 'supplierDetails':
        activeKey = '3';
        break;
        case 'grn':
        activeKey = '8';
        break;
        case 'inventory':
        activeKey = '25';
        break;
        case 'reports':
        activeKey = '21';
              // defaultOpen= 'sub1';
        break; 
        // case 'supplierDetails':
        // activeKey = '9';
        // break;
        // case 'createFeature':
        // activeKey = '5';
        // break;
      case 'createPO':
        activeKey = '6';
        break;
      case 'roleManage':
          activeKey = '11';
          defaultOpen= ['sub1'];
          break;
      case 'userManage':
          activeKey = '12';
          defaultOpen= ['sub1'];
            break;
      case 'createClassification':
          activeKey = '13';
          defaultOpen= ['sub1','sub2'];
          break;
      case 'createFeature':
            activeKey = '14';
            defaultOpen= ['sub1','sub2'];
            break;
      case 'addProducts':
            activeKey = '16';
            defaultOpen= ['sub1'];
            break;      
      case 'supplier':
            activeKey = '18';
            defaultOpen=['sub1'];
            break; 
      case 'createTags':
            activeKey = '15';
            defaultOpen= ['sub1','sub2'];
            break;
      case 'createTasks':
            activeKey = '19';
            defaultOpen= ['sub1'];
            break;
           
        default:
          activeKey = '1';
      }
      return {
        activeKey,
        defaultOpen
      };
    }

    static getDerivedStateFromProps(nextProps, nextState) {
      const getActiveMenuId = App.showMenuSelected(nextProps.match.url);

      // console.log("getActiveMenuId.defaultOpen",getActiveMenuId.defaultOpen)
      
      return {
        activemenuKey: getActiveMenuId.activeKey,
        defaultOpen: getActiveMenuId.defaultOpen
      };
    }

  handleOk = () => {
    if (this.state.clasiModal == true) {
      this.setState({ clasiModal: false });
    }
  };

  handleCancel = () => {
    if (this.state.clasiModal == true) {
      this.setState({ clasiModal: false });
    }
  };



  hide = () => {
    // console.log("In Hide.")
    // this.setState({
    //   visiblePop: false,
    // });
  };

  handleVisibleChange = visiblePop => {
    this.setState({ visiblePop });
  };

  


 onLogout=()=>{

  // console.log("In Logout..::")
  let logout = "https://api.sigmaproductmaster.webdezign.uk/api/logout"
   axios.get(logout).then((response) => {
      // console.log("Response...::",response.data)
      if(response){
        this.props.history.push('/')
      // this.setState({
      //   // prodData: response.data,
      //   // prodloading: false
      // })
    }else{
      this.setState({
        // prodData: response.data,
        // prodloading: false
      })
    }
    })
  
 }

  render(){
      
    const { collapsed,activemenuKey } = this.state;
const openNotification = placement => {
    notification.info({
      message: `Notification `,
      description: '1 Product Out of Stock.',
      placement,
    });
  };
    const menu = (
      <Menu onClick={this.handleMenuClick} style={{width:"110%",height:"120%"}}>
        <Menu.Item key="31"><Link to="/profile" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}}>My Profile</Link></Menu.Item>
        <Menu.Item key="32" onClick={()=>this.onLogout()}>
          <Link to="/" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}}>Logout
        </Link>
        </Menu.Item>
        {/* <Menu.Item key="33">Clicking me will close the menu.</Menu.Item> */}
      </Menu>
    );
    // const menu1 = (
    //   <Menu onClick={this.handleMenuClick} style={{width:"200%",height:"120%",marginTop:"30%"}}>
    //     <Menu.Item key="35"><Link to="/profile" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}}>My Profile</Link></Menu.Item>
    //     <Menu.Item key="36" onClick={()=>this.onLogout()}><Link to="/" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}} >Logout</Link></Menu.Item>
    //   </Menu>
    // );

  const text = <span style={{fontSize:"20px"}}>Notification</span>;
  const content = (
  <div>
    <p>Content</p>
    <p>Content</p>
  </div>
  
);
  return (
    <MediaQuery minDeviceWidth={700}>
      {(matches) => {
          if (matches) {
          return (
    <>

      <div> 
      
       <Layout>
         <Header className="header" style={{width: '100%', background: '#fdfdfd', height: '70px', padding: "0px"}}> 
         
           <img alt="example" src="./sigma_logo.png" style={{maxHeight:'70px',marginLeft:'0px'}}/>
           
           {/* <div style={{backgroundColor:"black"}}> */}
           {/* <img alt="example" src="./sigma_logo.png" style={{maxHeight:'70px',marginLeft:'50px'}}/> */}
             
             {this.props.header}
             
             {/* </div> */}
          
           {/* <Menu mode="horizontal" 
      selectedKeys={[activemenuKey]}
      defaultOpenKeys={[this.state.defaultOpen]}
      // style={{minHeight: 900}}
      theme="light"
      inlineCollapsed={this.state.collapsed}
      >
        <Menu.Item key="2" style={{fontWeight:'bold',fontSize: 18,}}>
        <Link to="/">
          <img alt="example" src="./sigma_logo.png" style={{maxHeight:'70px',marginLeft:'0px'}}/>
          </Link>
        </Menu.Item>
      
        
        <Menu.Item key="2" style={{fontWeight:'bold',fontSize: 20,}}>
        <Link to="/products" style={{marginLeft:"80px"}}>
          Product
          </Link>
        </Menu.Item>

         
      </Menu> */}
      
        {/* <Popover
        style={{width:"150%"}}
        content={<p><Link to="/profile">My Profile</Link> <br/><br/>
                 &nbsp;<Link to="/">Logout</Link></p>}
      >
      <Button type="link" style={{marginTop:'15px', float:"right", marginRight: 20, color:'#213A87',fontWeight:'bold',fontSize: 20,}} onClick={()=>this.setState({form: true})}>
        Sushant Chari <Button type="link" icon={<CaretDownOutlined />} style={{color:'#213A87',fontWeight:'bold',fontSize: 20,}}></Button>
        </Button >
         </Popover> */}

       <Dropdown 
        overlay={menu}
        // onVisibleChange={this.handleVisibleChange}
        // visible={this.state.visible}
      >
        <a className="ant-dropdown-link"
        style={{marginTop:'10px', float:"right", marginRight: 20, color:'#213A87',fontWeight:'bold',fontSize: "19px"}} 
        onClick={e => e.preventDefault()}>
        {this.state.userDetails.firstname} {this.state.userDetails.lastname} <CaretDownOutlined />
        </a>
      </Dropdown>

      {/* <Dropdown 
        overlay={menu1}
        // onVisibleChange={this.handleVisibleChange}
        // visible={this.state.visible}
      > */}
      {/* <Popover placement="bottomRight" title={text} content={content} style={{backgroundColor:"grey"}}> */}
         <Button type="link" style={{marginTop:'10px', float:"right", marginRight: 15, color:'#213A87',fontWeight:'bold',fontSize:"30px"}} 
         onClick={()=>this.setState({notif: !this.state.notif})}
        // onClick={() => openNotification('topRight')}
        >
         <BellTwoTone />
         </Button >
        {/* </Popover> */}
         {/* </Dropdown> */}
         {/* {this.state.notif ? 
        <Card 
        style={{height:200,width:200,backgroundColor:"grey",float:"right",borderRadius:5,marginTop:"60px",zIndex:"100",position:"absolute"}}>

        </Card> 
        :null
        } */}
         {/* <div>
           <h4 style={{color:'#8F0021', fontWeight:'bold',fontSize:22,}}>Sushant Chari</h4>
           </div> */}
         </Header>
       <Layout>
       
       <Sider collapsed={collapsed} width="11%" style={{backgroundColor:"#393E46"}}>
       {/* {this.props.sider} */}
      <div className="logo" />
      
      <Menu mode="inline" 
      selectedKeys={[activemenuKey]}
      defaultOpenKeys={this.state.defaultOpen}
      style={{minHeight: 900,backgroundColor:"#393E46"}}
      theme="dark"
      // inlineCollapsed={this.state.collapsed}
      >
        <Button block size="large" type="primary" onClick={()=>this.setState({collapsed: !this.state.collapsed})} 
               style={{float:'inline-start',backgroundColor:"#393E46", borderColor:"#393E46", fontSize:26,textAlign:'right',marginBottom: 15}}
        >
          {/* '#001529' */}
          {React.createElement(this.state.collapsed ? MenuUnfoldOutlined : MenuFoldOutlined)}
      </Button>

        {/* <Button type="primary" onClick={()=>this.setState({collapsed: !this.state.collapsed})} 
               style={{ margin:10, backgroundColor:'#353b8d', borderColor:'#353b8d'}}
        >
          {React.createElement(this.state.collapsed ? MenuUnfoldOutlined : MenuFoldOutlined)}
      </Button> */}
        {/* <Menu.Item key="1" style={{fontWeight:'bold',fontSize: "15px",marginTop:15}} icon={<HomeOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/home">
          Home
        </Link>
        </Menu.Item> */}

        <Menu.Item key="2" style={{fontWeight:'bold',fontSize: "15px",marginTop:15}} icon={<CodeSandboxCircleFilled  style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/products">
          Product
          </Link>
        </Menu.Item>

        <Menu.Item key="3" style={{fontWeight:'bold',fontSize: "15px",marginTop:15}} icon={<CodeSandboxCircleFilled  style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/supplierDetails">
          Supplier
          </Link>
        </Menu.Item>

        {/* <Menu.Item key="4" style={{fontWeight:'bold',fontSize: 16,marginTop:15}} icon={<ShopOutlined  style={{fontWeight:'bold',fontSize: 18}}/>}>
        <Link to="/supplier">
        Supplier List
        </Link>
        </Menu.Item> */}

         <Menu.Item key="6" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<AppstoreAddOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/createPO">
          PO
        </Link>
        </Menu.Item>
        
        <Menu.Item key="8" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<PieChartOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/grn">
          GRN
          </Link>
        </Menu.Item>

        <Menu.Item key="25" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<InboxOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/inventory">
          Inventory
          </Link>
        </Menu.Item>

        <Menu.Item key="21" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<BarChartOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}> 
              <Link to="/reports">Reports</Link>
        </Menu.Item>

        {/* <Menu.Item key="4" style={{fontWeight:'bold',fontSize: 18,marginTop:20}} icon={<ClusterOutlined style={{fontWeight:'bold',fontSize: 20}}/>}>
        <Link to="/createClassification">
          Classification
        </Link>
        </Menu.Item>
        <Menu.Item key="5" style={{fontWeight:'bold',fontSize: 18,marginTop:20}} icon={<ProjectOutlined  style={{fontWeight:'bold',fontSize: 20}}/>}>
        <Link to="/createFeature">
          Features
        </Link>
        </Menu.Item> */}
       
        <SubMenu key="sub1" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<TeamOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>} 
        title="Admin"
        // {
        //   <span>
        //     <Link to="/roleManage">
        //       <span>Admin</span>
        //     </Link>
        //   </span>
        // }
        >
            
            <Menu.Item key="11" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/roleManage">Role Management</Link></Menu.Item>
            
            <Menu.Item key="12" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/userManage">User Management</Link></Menu.Item>
            
            <Menu.Item key="18" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/supplier">Supplier List</Link></Menu.Item>

            <Menu.Item key="16" style={{fontWeight:'bold',fontSize: "15px"}}>
            <Link to="/productlist">Products</Link></Menu.Item>
            
            

            <Menu.Item key="19" style={{fontWeight:'bold',fontSize: "15px"}}>
            <Link to="/createTasks">Tasks</Link></Menu.Item>

            {/* <Menu.Item key="20" style={{fontWeight:'bold',fontSize: "15px"}}> 
              <Link to="/reports">Reports</Link>
            </Menu.Item> */}

            {/* <SubMenu key="sub2" title="MetaData"> */}
            <SubMenu key="sub2" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} 
        title={<span>
        {/* <Icon type="book" /> */}
        {/* <Link to="/createClassification"> */}
        <span style={{ color: '#D3D3D3'}}>Meta Data</span>
        {/* </Link> */}
      </span>}
        >
            {/* <Link to="/createClassification">
          </Link> */}
            {/* <Menu.Item key="13" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/createClassification">Classification</Link></Menu.Item> */}

            {/* <Menu.Item key="18" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/supplier">Supplier List</Link></Menu.Item> */}
            
            {/* <Menu.Item key="14" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/createFeature">Features</Link></Menu.Item> */}

            <Menu.Item key="15" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/createTags">Tags</Link></Menu.Item>

          </SubMenu>
            
            

        </SubMenu> 
         {/* <Menu.Item key="3" style={{fontWeight:'bold',fontSize: 18,marginTop:20}} icon={<ProfileOutlined style={{fontWeight:'bold',fontSize: 20}}/>}>
          <Link to="/profile">
          Profile
          </Link>
        </Menu.Item>
        <Menu.Item key="7" style={{fontWeight:'bold',fontSize: 18,marginTop:20}} icon={<LogoutOutlined style={{fontWeight:'bold',fontSize: 20}}/>}>
          <Link to="/profile">
          Logout
          </Link>
        </Menu.Item> */}
        {/* <Button block type="primary" onClick={()=>this.setState({collapsed: !this.state.collapsed})} 
               style={{float:'inline-start',backgroundColor:'#0d1142', borderColor:'#0d1142'}}
        >
          {React.createElement(this.state.collapsed ? MenuUnfoldOutlined : MenuFoldOutlined)}
      </Button> */}
      </Menu>
    </Sider>
           {/* <Button type="primary" onClick={()=>this.setState({collapsed: !this.state.collapsed})} 
               style={{backgroundColor:'#353b8d', borderColor:'#353b8d',margin: 5}}
        >
          {React.createElement(this.state.collapsed ? MenuUnfoldOutlined : MenuFoldOutlined)}
      </Button> */}
       
       <Content style={{margin: "10px 10px", padding: 2,background: "#fff",minHeight:450}}>
         {this.state.notif ? 
        <Card 
        style={{height:200,width:400,marginLeft:"62%",boxShadow:"1px 3px 1px #9E9E9E",marginRight:"2%",borderRadius:5,marginTop:"10px",zIndex:"1000",position:"absolute"}}>
          <CloseOutlined style={{float:"right"}} onClick={()=>this.setState({notif: false})}/>
          <h1 style={{textAlign:"center",fontSize:"22px",fontWeight:"bold"}}>Notification</h1>
        </Card> 
        :null
        }
           {this.props.children}
       </Content>
       </Layout>
       {/* <Footer style={{ textAlign: 'center',marginTop:'1%'}}> 
       © 2021 All rights reserved. <a href="https://www.webdezign.co.uk" target="_blank">Developed by Webdezign</a>
       </Footer> */}
       </Layout>
       {/* } */}
    </div>

     </> 

     )
      }
      else{
        <div>
          <Appm />
        </div>
      }
      }
      }
      
   </MediaQuery>
  );
  }
}

export default withRouter(App);
// export default App;

// {
//    <Switch>
//                <Route exact path="/" component={App} />
//                <Route path="./components/mProfile" component={Profile} />
//                 <Route path="./components/mEnquiry" component={Enquiry} />
//                 <Route
//                   path="./components/mAppointments"
//                   component={Appointments}
//                 />
//               </Switch>
// }

