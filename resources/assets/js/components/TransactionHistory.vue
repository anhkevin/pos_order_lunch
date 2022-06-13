<template>
    <div class="card">
        <div class="card-header d-block d-sm-flex border-0">
            <div>
                <h4 class="fs-20 text-black">Lịch sử giao dịch</h4>
            </div>
        </div>
        <div class="payment-bx tab-content p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Loại giao dịch</th>
                            <th>Ghi chú</th>
                            <th>Số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="post" v-for="history in posts" v-bind:key="history.id">
                            <td>{{ history.date }}</td>
                            <td>{{ history.type }}</td>
                            <td>{{ history.note }}</td>
                            <td>{{ history.amount }}</td>
                        </tr>
                    </tbody>
                </table>
                <div style="text-align: center;padding-bottom: 15px;">
                    <button v-bind:class="[isFinished ? 'btn btn-primary finish' : 'btn btn-primary load-more']" @click='getPosts()' v-cloak type="button">
                        <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> {{ buttonText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user_id'],
        data() {
            return {
                isFinished: false,
                row: 0, // Record selction position
                rowperpage: 5, // Number of records fetch at a time
                buttonText: 'Load More',
                posts: ''
            }
        },
        methods: {
            getPosts: function(){
                var app = this;
                axios.post('/my-wallet/load-history', {
                    row: app.row, 
                    rowperpage: app.rowperpage,
                    user_id: app.user_id
                })
                .then(function (response) {

                    if(response.data !='' ){

                        // Update rowperpage
                        app.row+=app.rowperpage;

                        var len = app.posts.length;
                        if(len > 0){
                            app.buttonText = "Loading ...";
                            setTimeout(function() {
                                app.buttonText = "Load More";

                                // Loop on data and push in posts
                                for (let i = 0; i < response.data.length; i++){
                                app.posts.push(response.data[i]); 
                                } 
                            },500);
                        }else{
                            app.posts = response.data;
                        }

                    }else{
                        app.buttonText = "No more records avaiable.";
                        app.isFinished = true;
                    }
                });
            }
        },
        created: function(){
            this.getPosts();
        }
    }
</script>
