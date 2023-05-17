import { Component, Input, OnInit } from '@angular/core';
import { FlightInfoResponse } from '../_models/Flights/FlightInfoResponse';
import { FlightResponses } from '../_models/Flights/FlightResponses';
import { FlightService } from '../_services/flight.service';

@Component({
  selector: 'app-show-flightoffers',
  templateUrl: './show-flightoffers.component.html',
  styleUrls: ['./show-flightoffers.component.css']
})
export class ShowFlightoffersComponent implements OnInit{
  @Input() offers!: FlightResponses;
  @Input() formSubmitted!: boolean;
  flightInfo = {} as FlightInfoResponse;
  flightChosen: boolean = false;
  moreOffers: boolean = true;
  shownOffers: number = 10;
  private paginationCount: number = 10;


  constructor(private _flightService: FlightService) { }

  ngOnInit(): void {
    this.offers.data.length > this.paginationCount ? this.moreOffers == true : this.moreOffers == false;
  }

  chooseFlight(id: string) {
    this._flightService.getFlightInfo(this.offers.data[parseInt(id) - 1]).subscribe(info => {
      this.flightChosen = true;
      this.flightInfo = info;
    })
  }

  showMoreOffers(){
    if (this.shownOffers + this.paginationCount > this.offers.data.length) {
      this.shownOffers = this.offers.data.length;
      this.moreOffers = false;
    }
    else this.shownOffers += this.paginationCount;
  }

  reset() {
    this.flightChosen = false;
    this.shownOffers = this.paginationCount;
    this.moreOffers = true;
  }
}
