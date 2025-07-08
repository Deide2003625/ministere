/* eslint-disable camelcase */
import { sentenceCase } from 'change-case';
import { useEffect, useState } from 'react';
import { Link as RouterLink } from 'react-router-dom';
// @mui
import { useTheme } from '@mui/material/styles';
import {
  Card,
  Table,
  Avatar,
  Button,
  Checkbox,
  TableRow,
  TableBody,
  TableCell,
  Container,
  Typography,
  TableContainer,
  TablePagination,
  Stack,
} from '@mui/material';
// routes
import { getRequest } from '../../utils/api';
import { PATH_DASHBOARD } from '../../routes/paths';
// hooks
import useSettings from '../../hooks/useSettings';

// components
import Page from '../../components/Page';
import Label from '../../components/Label';
import Iconify from '../../components/Iconify';
import Scrollbar from '../../components/Scrollbar';
import SearchNotFound from '../../components/SearchNotFound';
import HeaderBreadcrumbs from '../../components/HeaderBreadcrumbs';
// sections
import { UserListHead, UserListToolbar, UserMoreMenu } from '../../sections/@dashboard/user/list';

// ----------------------------------------------------------------------

const TABLE_HEAD = [
  { id: 'denomination', label: 'Dénomination', alignRight: false },
  { id: 'statut_legal', label: "Type d'association", alignRight: false },
  { id: 'company', label: 'Adresse Email', alignRight: false },
  { id: 'status', label: 'Statut', alignRight: false },
  { id: 'isVerified', label: "Date d'enregistrement", alignRight: false },
  { id: '' },
];

// ----------------------------------------------------------------------

export default function TreatedDemands() {
  const theme = useTheme();
  const { themeStretch } = useSettings();

  const [page, setPage] = useState(0);
  const [order, setOrder] = useState('asc');
  const [selected, setSelected] = useState([]);
  const [orderBy, setOrderBy] = useState('denomination');
  const [filterName, setFilterName] = useState('');
  const [rowsPerPage, setRowsPerPage] = useState(5);
  const [requestList, setRequestList] = useState();
  const [filteredRequests, setFilteredRequests] = useState();
  const [isNotFound, setIsNotFound] = useState();

  useEffect(() => {
    const fetchUsers = async () => {
      try {
        const response = await getRequest('/api/get_approved');
        const requests = response.data.records;
        console.log('Données traitées reçues:', requests);
        if (requests && requests.length > 0) {
          setRequestList(requests);
          setFilteredRequests(applySortFilter(requests, getComparator(order, orderBy), filterName));
          setIsNotFound(false);
        } else {
          setRequestList([]);
          setFilteredRequests([]);
          setIsNotFound(true);
        }
      } catch (error) {
        console.log('Erreur lors de la récupération des données traitées:', error);
        setRequestList([]);
        setFilteredRequests([]);
        setIsNotFound(true);
      }
    };

    fetchUsers();
  }, [order, orderBy, filterName]);

  const handleRequestSort = (property) => {
    const isAsc = orderBy === property && order === 'asc';
    setOrder(isAsc ? 'desc' : 'asc');
    setOrderBy(property);
  };

  const handleSelectAllClick = (checked) => {
    if (checked) {
      const newSelecteds = requestList.map((n) => n.denomination);
      setSelected(newSelecteds);
      return;
    }
    setSelected([]);
  };

  const handleClick = (denomination) => {
    const selectedIndex = selected.indexOf(denomination);
    let newSelected = [];
    if (selectedIndex === -1) {
      newSelected = newSelected.concat(selected, denomination);
    } else if (selectedIndex === 0) {
      newSelected = newSelected.concat(selected.slice(1));
    } else if (selectedIndex === selected.length - 1) {
      newSelected = newSelected.concat(selected.slice(0, -1));
    } else if (selectedIndex > 0) {
      newSelected = newSelected.concat(selected.slice(0, selectedIndex), selected.slice(selectedIndex + 1));
    }
    setSelected(newSelected);
  };

  const handleChangeRowsPerPage = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0);
  };

  const handleFilterByName = (filterName) => {
    setFilterName(filterName);
    setPage(0);
  };

  const handleDeleteMultiUser = (selected) => {
    const deleteUsers = requestList.filter((user) => !selected.includes(user.denomination));
    setSelected([]);
    setRequestList(deleteUsers);
  };

  const emptyRows = page > 0 ? Math.max(0, (1 + page) * rowsPerPage - requestList.length) : 0;

  const formatDate = (dateFromResponse) => dateFromResponse.split(' ')[0];

  return (
    <Page title="Demandes traitées">
      <Container maxWidth={themeStretch ? false : 'lg'}>
        <HeaderBreadcrumbs
          heading="Demandes traitées"
          links={[
            { name: 'Dashboard', href: PATH_DASHBOARD.root },
            { name: 'Demandes', href: PATH_DASHBOARD.demandes.enAttente },
            { name: 'Traitées' },
          ]}
        />

        <Card>
          <UserListToolbar
            numSelected={selected.length}
            filterName={filterName}
            onFilterName={handleFilterByName}
            onDeleteUsers={() => handleDeleteMultiUser(selected)}
          />

          <Scrollbar>
            <TableContainer sx={{ minWidth: 800 }}>
              <Table>
                <UserListHead
                  order={order}
                  orderBy={orderBy}
                  headLabel={TABLE_HEAD}
                  rowCount={requestList ? requestList.length : 0}
                  numSelected={selected.length}
                  onRequestSort={handleRequestSort}
                  onSelectAllClick={handleSelectAllClick}
                />
                <TableBody>
                  {requestList && filteredRequests ? (
                    filteredRequests.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage).map((row) => {
                      const { id, denomination, statut_legal, email, statut_association, code_requete, updated_at } = row;
                      const isItemSelected = selected.indexOf(denomination) !== -1;

                      return (
                        <TableRow
                          hover
                          key={id}
                          tabIndex={-1}
                          role="checkbox"
                          selected={isItemSelected}
                          aria-checked={isItemSelected}
                        >
                          <TableCell padding="checkbox">
                            <Checkbox checked={isItemSelected} onClick={() => handleClick(denomination)} />
                          </TableCell>
                          <TableCell>
                            <Typography variant="subtitle2" noWrap>
                              {denomination}
                            </Typography>
                          </TableCell>
                          <TableCell align="left">{statut_legal}</TableCell>
                          <TableCell align="left">{email}</TableCell>
                          <TableCell align="left">
                            <Label
                              variant={theme.palette.mode === 'light' ? 'ghost' : 'filled'}
                              color={statut_association === 'inactive' ? 'error' : 'success'}
                            >
                              {sentenceCase(statut_association)}
                            </Label>
                          </TableCell>
                          <TableCell align="left">{updated_at ? formatDate(updated_at) : "--"}</TableCell>

                          <TableCell align="right">
                            <UserMoreMenu assocCode={`${code_requete}`} currentStatus={`${statut_association}`} />
                          </TableCell>
                        </TableRow>
                      );
                    })
                  ) : (
                    <TableRow>
                      <TableCell />
                      <TableCell />
                      <TableCell />
                      <TableCell>Aucune donnée.</TableCell>
                      <TableCell />
                    </TableRow>
                  )}
                  {emptyRows > 0 && (
                    <TableRow style={{ height: 53 * emptyRows }}>
                      <TableCell colSpan={6} />
                    </TableRow>
                  )}
                </TableBody>
                {isNotFound && (
                  <TableBody>
                    <TableRow>
                      <TableCell align="center" colSpan={6} sx={{ py: 3 }}>
                        <SearchNotFound searchQuery={filterName} />
                      </TableCell>
                    </TableRow>
                  </TableBody>
                )}
              </Table>
            </TableContainer>
          </Scrollbar>

          <TablePagination
            rowsPerPageOptions={[5, 10, 25]}
            component="div"
            count={requestList ? requestList.length : 0}
            rowsPerPage={rowsPerPage}
            page={page}
            onPageChange={(e, page) => setPage(page)}
            onRowsPerPageChange={handleChangeRowsPerPage}
          />
        </Card>
      </Container>
    </Page>
  );
}

// ----------------------------------------------------------------------

function descendingComparator(a, b, orderBy) {
  if (b[orderBy] < a[orderBy]) {
    return -1;
  }
  if (b[orderBy] > a[orderBy]) {
    return 1;
  }
  return 0;
}

function getComparator(order, orderBy) {
  return order === 'desc'
    ? (a, b) => descendingComparator(a, b, orderBy)
    : (a, b) => -descendingComparator(a, b, orderBy);
}

function applySortFilter(array, comparator, query) {
  const stabilizedThis = array.map((el, index) => [el, index]);
  stabilizedThis.sort((a, b) => {
    const order = comparator(a[0], b[0]);
    if (order !== 0) return order;
    return a[1] - b[1];
  });
  if (query) {
    return array.filter((_user) => _user.denomination.toLowerCase().indexOf(query.toLowerCase()) !== -1);
  }
  return stabilizedThis.map((el) => el[0]);
}
