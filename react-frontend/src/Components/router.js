import React from 'react'
import { Route, BrowserRouter as Router, HashRouter } from "react-router-dom";
import Login from '../Components/login';
import Products from '../Components/products';
import Home from '../Components/home';
import UserManagement from '../Components/userManage'
import RoleManagement from '../Components/roleManage'
import Profile from "../Components/profile"
import CreatePO from "../Components/createPO"
import CreateFeature from "../Components/createFeature"
import CreateClassification from "../Components/createClassification"
import Supplier from "./supplier"
import ChildProducts from "./childProducts"
import AddProduct from "./productlist"
import SupplierDetails from "./supplierDetails"
import PricingDetails from "./pricingDetails"
import Grn from "./grn"
import Inventory from "./inventory"
import CreateTags from "./createTags"
import CreateTasks from "./createTasks"
import Reports from "./reports"
import PowerBI from "./powerBI"

const Routers =()=>(
    
    <Router>
        <HashRouter>
        <React.Fragment>
        <Route exact path="/" component={Login} />
        <Route exact path="/home" component={Home} />
        <Route exact path="/products" component={Products} />
        <Route  path="/supplierDetails" component={SupplierDetails} />
        <Route  path="/pricingDetails" component={PricingDetails} />
        <Route exact path="/childProducts" component={(ChildProducts)}/>
        <Route  path="/grn" component={Grn} />
        <Route  path="/inventory" component={Inventory} />
        <Route  path="/supplier" component={Supplier} />
        <Route  path="/userManage" component={UserManagement} />
        <Route  path="/roleManage" component={RoleManagement} />
        <Route  path="/profile" component={Profile} />
        <Route  path="/createPO" component={CreatePO} />
        <Route  path="/createTags" component={CreateTags} />
        <Route  path="/createTasks" component={CreateTasks} />
        <Route  path="/createFeature" component={CreateFeature} />
        <Route  path="/productlist" component={AddProduct} />
        <Route  path="/reports" component={Reports}/>
        <Route  path="/powerBI" component={PowerBI}/>
        <Route  path="/createClassification" component={CreateClassification} />
        </React.Fragment>
        </HashRouter>
    </Router>
    
);
export default Routers   