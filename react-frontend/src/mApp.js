import React from 'react'
import './App.css';
import { Button, Table, Layout, Menu, Popover, Dropdown, Card, notification,Icon} from "antd";
import { Drawer, List, NavBar, Button as MButton,Accordion, Icon as MIcon } from 'antd-mobile';

import { Link, BrowserRouter as Router, withRouter, Switch} from 'react-router-dom';


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

class Appm extends React.Component{
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
    };
  }

  componentDidMount(){
    setTimeout(() => {  this.setState({landingModal: true}) }, 5000);

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
        break; 
        
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
      const getActiveMenuId = Appm.showMenuSelected(nextProps.match.url);

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
    console.log("In Hide.")
    // this.setState({
    //   visiblePop: false,
    // });
  };

  handleVisibleChange = visiblePop => {
    this.setState({ visiblePop });
  };

  


 
  render(){

    const { children } = this.props;
      // console.log("props.children",children);

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
        <Menu.Item key="32"><Link to="/" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}}>Logout</Link></Menu.Item>
      </Menu>
    );
    const menu1 = (
      <Menu onClick={this.handleMenuClick} style={{width:"200%",height:"120%",marginTop:"30%"}}>
        <Menu.Item key="35"><Link to="/profile" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}}>My Profile</Link></Menu.Item>
        <Menu.Item key="36"><Link to="/" style={{color:'#213A87',fontWeight:'bold',fontSize: 20}}>Logout</Link></Menu.Item>
      </Menu>
    );

    const menuList = [
      {
        title: 'Products',
        to: '/jewelProduct',
        icon: 'code-sandbox'
      },
     
    ];

    const sidebar = menuList.map((m, i) => {
      // if(m.to){
        return (
          <Menu mode="inline" 
          selectedKeys={[activemenuKey]}
          defaultOpenKeys={this.state.defaultOpen}
          style={{minHeight: 900,minWidth: 180, backgroundColor:"#393E46",}}
          theme="dark"
          >   
             {/* <Menu.Item key="1" style={{fontWeight:'bold',fontSize: "13px",marginTop:15}} icon={<HomeOutlined style={{fontWeight:'bold',fontSize: "15px"}}/>}>
              <Link to="/home">
                Home
              </Link>
              </Menu.Item> */}

              <Menu.Item key="2" style={{fontWeight:'bold',fontSize: "13px",marginTop:15}} icon={<CodeSandboxCircleFilled  style={{fontWeight:'bold',fontSize: "15px"}}/>}>
              <Link to="/products">
                Product
                </Link>
              </Menu.Item>

               <Menu.Item key="3" style={{fontWeight:'bold',fontSize: "15px",marginTop:15}} icon={<CodeSandboxCircleFilled  style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/supplierDetails">
          Supplier
          </Link>
        </Menu.Item>

         <Menu.Item key="6" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<AppstoreAddOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/createPO">
          PO
        </Link>
        </Menu.Item>
        {/*
        <Menu.Item key="8" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<PieChartOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/grn">
          GRN
          </Link>
        </Menu.Item>
*/}
        {/* <Menu.Item key="25" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<InboxOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
        <Link to="/inventory">
          Inventory
          </Link>
        </Menu.Item> */}

        <Menu.Item key="21" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<BarChartOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}> 
              <Link to="/reports">Reports</Link>
        </Menu.Item> 

        <SubMenu key="sub1" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<TeamOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>} 
        title="Admin"
      
        >
            
            <Menu.Item key="11" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/roleManage">Role Management</Link></Menu.Item>
            
            <Menu.Item key="12" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/userManage">User Management</Link></Menu.Item>
            
            <Menu.Item key="18" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/supplier">Supplier List</Link></Menu.Item>

            <Menu.Item key="16" style={{fontWeight:'bold',fontSize: "15px"}}>
            <Link to="/addProducts">Products</Link></Menu.Item>

            <Menu.Item key="19" style={{fontWeight:'bold',fontSize: "15px"}}>
            <Link to="/createTasks">Tasks</Link></Menu.Item>

            {/* <SubMenu key="sub2" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} 
        title={<span> */}
       
        {/* <Link to="/createClassification"> */}
        {/* <span style={{ color: '#D3D3D3'}}>Meta Data</span> */}
        {/* </Link> */}
      {/* </span>}
        > */}
            
            {/* <Menu.Item key="13" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/createClassification">Classification</Link></Menu.Item>
            
            <Menu.Item key="14" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/createFeature">Features</Link></Menu.Item> */}
{/* 
            <Menu.Item key="15" style={{fontWeight:'bold',fontSize: "15px"}}>
              <Link to="/createTags">Tags</Link></Menu.Item>
          </SubMenu> */}
            
            

        </SubMenu> 
          </Menu>
    )
      //   }else{
      //     return (
      //       <Menu mode="inline" 
      //       selectedKeys={[activemenuKey]}
      //       defaultOpenKeys={this.state.defaultOpen}
      //       style={{minHeight: 900,backgroundColor:"#393E46",}}
      //       theme="dark"
      //       >   
      //          <Menu.Item key="3" style={{fontWeight:'bold',fontSize: "15px",marginTop:15}} icon={<CodeSandboxCircleFilled  style={{fontWeight:'bold',fontSize: "17px"}}/>}>
      //   <Link to="/supplierDetails">
      //     Supplier
      //     </Link>
      //   </Menu.Item>

      //    <Menu.Item key="6" style={{fontWeight:'bold',fontSize: "15px",marginTop:20}} icon={<AppstoreAddOutlined style={{fontWeight:'bold',fontSize: "17px"}}/>}>
      //   <Link to="/createPO">
      //     PO
      //   </Link>
      //   </Menu.Item>
  
      //       </Menu>
      // )
      //   }
    })

    // const sidebar1 = (<List style={{width:"150%"}}>
    //   {[0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15].map((i, index) => {
    //     if (index === 0) {
    //       return (<List.Item key={index}
    //         thumb="https://zos.alipayobjects.com/rmsportal/eOZidTabPoEbPeU.png"
    //         multipleLine
    //       >Category</List.Item>);
    //     }
    //     return (<List.Item key={index}
    //       thumb="https://zos.alipayobjects.com/rmsportal/eOZidTabPoEbPeU.png"
    //     >Category{index}</List.Item>);
    //   })}
    // </List>)
 
  return (
    <>

      <div> 
      
       <Layout>
         <Header className="header" style={{width: '100%', background: '#fdfdfd', height: '60px', padding: "0px",position:"fixed"}}> 
         
         <Button onClick={()=>this.setState({collapsed: !this.state.collapsed})} 
               style={{backgroundColor:"#fff",color:"#000000",borderColor:"#fff", fontSize:22,textAlign:'left',margin:"1%",}}
        >

       {/* <div type="menu-unfold" size="lg" color="#000" onClick={()=>this.setState({collapsed: !this.state.collapsed})}> */}
          {React.createElement(this.state.collapsed ? MenuUnfoldOutlined : MenuFoldOutlined)}
        {/* </div> */}
      </Button>  
       <img alt="example" src="./sigma_logo.png" style={{maxHeight:'50px',marginLeft:'0px'}}/>
           
             {this.props.header}
          
       
         </Header>
         {/* <NavBar
          mode="light"
          leftContent={[
            <div>
              <h4 style={{color:'#48486c', fontWeight:'bold',fontSize:"19px",marginTop:"10%"}}>Test</h4>
            </div>
          ]}
        /> */}
       <Layout>
       
       <Drawer
          className="admin-drawer"
          style={{
            minHeight: document.documentElement.clientHeight,
            width: "100%",
            marginTop: '14%'
          }}
          // enableDragHandle
          contentStyle={{
            textAlign: 'center',
            overflow: 'auto',
            minHeight: document.documentElement.clientHeight,
            padding: '10px'
          }}
          sidebar={sidebar}
          open={this.state.collapsed}
          onOpenChange={()=>{this.setState({collapsed:!this.state.collapsed})}}
          sidebarStyle={{ background: '#001529', zIndex: '5' }}
        >
          {/* {children} */}
        </Drawer>
      
           
       
       <Content style={{margin: "5px 5px",marginTop: "15%", padding: 2,background: "#f8f9f9",minHeight:650}}>
         {this.state.notif ? 
        <Card 
        style={{height:200,width:400,marginLeft:"62%",boxShadow:"1px 3px 1px #9E9E9E",marginRight:"2%",borderRadius:5,marginTop:"10px",zIndex:"1000",position:"absolute"}}>
          <CloseOutlined style={{float:"right"}} onClick={()=>this.setState({notif: false})}/>
          <h1 style={{textAlign:"center",fontSize:"22px",fontWeight:"bold"}}>Notification</h1>
        </Card> 
        :null
        }
        {/* <NavBar
        // style={{position:"fixed"}}
          mode="light"
          leftContent={[
            <div>
              <h4 style={{color:'#48486c', fontWeight:'bold',fontSize:"19px",marginTop:"10%",position:"fixed"}}>{this.props.pageName}</h4>
            </div>
          ]}
        />  */}
           {this.props.children}
       </Content>
       </Layout>
       
       </Layout>
       
    </div>

     </> 
  );
  }
}

export default withRouter(Appm);

