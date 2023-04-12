// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
// // import { Head } from '@inertiajs/react';

// import React from 'react';
// import { Head, usePage, Link } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
// import { Link } from 'react-router-dom';
// import Button from 'react-bootstrap/Button'
import axios from 'axios';
// import Swal from 'sweetalert2'

// export default function Dashboard({ auth }) {
//     return (
//         <AuthenticatedLayout
//             user={auth.user}
//             header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
//         >
//             <Head title="Dashboard" />

//             <div className="py-12">
//                 <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
//                     <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
//                         <div className="p-6 text-gray-900">You're logged in!</div>
//                     </div>
//                 </div>
//             </div>
//         </AuthenticatedLayout>
//     );
// }

export default function List() {
    const [state, setState] = useState({
        value:'',
        show:''
    });
    
    const handleChange = (e) => {
        setState({value: e.target.value})
    }
    // const [products, setProducts] = useState([])

    // useEffect(()=>{
    //     fetchProducts() 
    // },[])

    // s.handleSubmit = this.handleSubmit.bind(this);
    //     }

    // const fetchProducts = async () => {
    //     await axios.get(`http://localhost:8000/api/get-hotel-details/1`).then(({data})=>{
    //     // console.log(data);
    //     setProducts(data)
    //     })
    // }

    function sayHello() {
        console.log({show: state.value});
    }

    return (
        // <div className="container">
        //     <div>
        //         <input type="text" value={state.value} onChange={(e)=>handleChange(e)} />
        //         <button onClick={sayHello}>
        //             Search
        //         </button>
        //     </div>

        //     <div className="row">
        //       <div className='col-12'>
        //       </div>
        //       <div className="col-12">
        //           <div className="card card-body">
        //               <div className="table-responsive">
        //                   <table className="table table-bordered mb-0 text-center">
        //                       <thead>
        //                           <tr>
        //                               <th>Hotel Name</th>
        //                               <th>Distance</th>
        //                               <th>Location</th>
        //                               <th>Phone Number</th>
        //                           </tr>
        //                       </thead>
        //                       <tbody>
        //                           {
        //                               products.length > 0 && (
        //                                   products.map((row, key)=>(
        //                                       <tr key={key}>
        //                                           <td>{row.hotel_name}</td>
        //                                           <td>{row.distance}</td>
        //                                           <td>{row.location}</td>
        //                                           <td>{row.phone_number}</td>
        //                                       </tr>
        //                                   ))
        //                               )
        //                           }
        //                       </tbody>
        //                   </table>
        //               </div>
        //           </div>
        //       </div>
        //     </div>
        // </div>
      )
}


